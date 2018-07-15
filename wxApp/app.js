App({
    onLaunch: function () {

    },
    onShow: function () {
        let that = this
        that.getUserInfoByRequest(function () {
            that.checkSessionAndLogin(function () {
                that.commonOnShow()
            })
        })
    },
    globalData: {
        user: null,
        apiDomain: "https://newlt.justyoujob.com/",
        qiNiuDomain: 'http://img.newlt.justyoujob.com/',
        systemInfo: null,
        logining: false,
    },
    checkTid: function (tid, success, fail, refresh) {
        let that = this,
            time = parseInt((new Date()).getTime() / 1000),
            user = that.globalData.user;
        if (!refresh && (tid == 0 || tid == user.tid) && user.expire_at && time < user.expire_at)
            return success()
        that.get("user/check-tid", {tid: tid}, function (res) {
            if (res.result)
                success()
            else
                fail()
        })
    },
    commonOnShow: function () {

    },
    post: function (url, params, success, fail, complete) {
        this.request("POST", url, params, success, fail, complete);
    },
    get: function (url, params, success, fail, complete) {
        this.request("GET", url, params, success, fail, complete);
    },
    request: function (method, url, params, success, fail, complete, loginFail = false) {
        let that = this
        if (url.substr(0, 5) != "https")
            url = that.globalData.apiDomain + url;
        wx.showLoading({
            title: "加载中..."
        })
        wx.request({
            url: url,
            data: params,
            method: method,
            dataType: "json",
            header: {
                "Content-Type": "application/x-www-form-urlencoded",
                cookie: wx.getStorageSync('cookie')
            },
            success: function (res) {
                wx.hideLoading()
                if (res.data != undefined && res.data.code != undefined) {
                    if (res.header['Set-Cookie'])
                        wx.setStorageSync('cookie', res.header['Set-Cookie']);
                    if (res.data.hasOwnProperty("msg") && res.data.msg != '' && (res.data.hasOwnProperty("code") && res.data.code != -1)) {
                        that.toast(res.data.msg, res.data.code == 0 ? "success" : "none")
                    }
                    if (res.data.code == 0) {
                        if (typeof success == 'function')
                            success(res.data.data, res.data);
                    } else if (res.data.code == -1) {
                        if (!loginFail) {
                            that.login(() => {
                                that.request(method, url, params, success, fail, complete, true)
                            });
                        }
                    } else if (typeof fail == 'function')
                        fail(res.data)
                } else
                    that.toast("500,服务器解析异常")
            },
            fail: (res) => {
                wx.hideLoading()
                if (typeof fail == 'function')
                    fail(res)
            },
            complete: function (e) {
                if (complete)
                    complete(e)
            }
        });
    },
    getUserInfo: function (success, refresh) {
        let that = this,
            user = that.globalData.user
        if (!user || refresh)
            that.getUserInfoByRequest(success)
        else if (success)
            success()
    },
    getUserInfoByRequest: function (success) {
        let that = this
        that.post("user/user-info", {}, function (data) {
            that.globalData.user = data.user
            if (success)
                success()
        })
    },
    setUserInfo: function (data, callBack) {
        let that = this;
        that.post("user/app-user", data, function (data) {
            that.globalData.user = data.user;
            if (typeof callBack == "function") {
                callBack();
            }
        })
    },
    checkSessionAndLogin: function (success) {
        let that = this
        wx.checkSession({
            success: function (e) {
                console.log(e)
                if(success) success()
            },
            fail: function () {
                that.login(success);
            }
        })
    },
    login: function (success) {
        let that = this;
        if (that.globalData.logining) {
            console.log(123)
            setTimeout(() => {
                that.checkSessionAndLogin(success)
            }, 1000)
            return true
        }
        that.globalData.logining = true
        wx.login({
            success: function (res) {
                console.log(res)
                if (res.code) {
                    that.post("user/app-login", {code: res.code}, data => {
                        that.globalData.user = data.user;
                        if (success)
                            success();
                    }, () => {
                    }, () => {
                        that.globalData.logining = false
                    })
                } else {
                    console.log('登录失败：' + res.errMsg)
                    that.globalData.logining = false
                }
            },
            fail: function (res) {
                console.log(res);
                that.globalData.logining = false
            }
        })
    },
    getSystemInfo: function (callBack) {
        let that = this
        if (!that.globalData.systemInfo) {
            wx.getSystemInfo({
                success: function (res) {
                    that.globalData.systemInfo = res
                    console.log(res)
                    if (typeof callBack == "function") {
                        callBack(res);
                    }
                }
            })
        } else if (typeof callBack == "function") {
            console.log(that.globalData.systemInfo)
            callBack(that.globalData.systemInfo);
        }
    },
    phoneCall: function (phone, success) {
        wx.makePhoneCall({
            phoneNumber: phone,
            success: success
        })
    },
    toast: (text, icon, callback) => {
        icon = icon == undefined ? "none" : icon
        wx.showToast({
            title: text,
            icon: icon,
            complete: callback
        })
    },
    confirm: (content, success, fail, title, confirmText, cancelText) => {
        wx.showModal({
            title: title == undefined ? "提示" : title,
            content: content,
            success: res => {
                if (res.confirm) {
                    if (typeof success == "function")
                        success()
                } else if (res.cancel) {
                    if (typeof fail == "function")
                        fail()
                }
            },
            confirmText: confirmText == undefined ? "确定" : confirmText,
            cancelText: cancelText == undefined ? "取消" : cancelText,
        })
    },
    getLocation: function (success, fail, type) {
        let that = this
        that.authorize("scope.userLocation", function () {
            that.getLocationAction(success, fail, type)
        }, function () {
            that.toast("请允许使用地理位置", "none")
            if (typeof fail == "function")
                fail()
        })
    },
    getLocationAction: function (success, fail, type) {
        let that = this
        type = type == undefined ? "gcj02" : type
        wx.getLocation({
            type: type,
            success: function (res) {
                if (typeof success == "function")
                    success(res)
            },
            fail: function (res) {
                that.toast("请开启定位设置", "none")
                if (typeof fail == "function")
                    fail(res)
            }
        })
    },
    authorize: function (scopeName, success, fail) {
        let that = this
        scopeName = scopeName.substr(0, 6) != "scope." ? "scope." + scopeName : scopeName
        wx.getSetting({
            success: (res) => {
                let setting = res.authSetting
                if (setting.hasOwnProperty(scopeName) && setting[scopeName]) {
                    if (typeof success == "function")
                        success()
                } else {
                    wx.authorize({
                        scope: scopeName,
                        success: success,
                        fail: function () {
                            let txt;
                            switch (scopeName) {
                                case "scope.userInfo" :
                                    txt = "用户信息"
                                    break
                                case "scope.userLocation" :
                                    txt = "地理位置"
                                    break
                                case "scope.address" :
                                    txt = "通讯地址"
                                    break
                                case "scope.invoiceTitle" :
                                    txt = "发票抬头"
                                    break
                                case "scope.werun" :
                                    txt = "微信运动步数"
                                    break
                                case "scope.record" :
                                    txt = "录音功能"
                                    break
                                case "scope.writePhotosAlbum" :
                                    txt = "保存到相册"
                                    break
                                case "scope.camera" :
                                    txt = "摄像头"
                                    break
                                default:
                                    break
                            }
                            that.toast("请允许小程序的 " + txt + " 权限", "none")
                        }
                    })
                }
            }
        })
    },
    setTitle: (title) => {
        wx.setNavigationBarTitle({
            title: title
        })
    },
    turnPage: (url, success) => {
        if (!url)
            return false
        if (url == "index/home" || url == "user/user") {
            wx.switchTab({
                url: "/pages/" + url,
                success: success
            })
        } else
            wx.navigateTo({
                url: "/pages/" + url,
                success: success
            })
    },
    formatSecondStr: function (s) {
        if (s <= 0)
            return "00:00";
        let day = Math.floor(s / 86400),
            hour = Math.floor(s / 3600) % 24,
            min = Math.floor(s / 60) % 60,
            second = Math.floor(s % 60),
            str = ""
        if (day > 0)
            str = day + "天 "
        str += this.PrefixInteger(hour, 2) + " : "
        str += this.PrefixInteger(min, 2) + " : "
        str += this.PrefixInteger(second, 2)
        return str;
    },
    PrefixInteger: (num, length) => {
        return (Array(length).join('0') + num).slice(-length);
    },
    fullImg: function (urls) {
        let domain = this.globalData.qiNiuDomain
        if (typeof urls == "Array") {
            let i = 0,
                src = []
            for (i; i < urls.length; i++) {
                src.push(this.fullImg(urls[i]))
            }
            return src
        } else if (typeof urls == "String")
            return urls.substr(0, 4) == "http" ? urls : domain + urls
        else
            return urls
    }
})