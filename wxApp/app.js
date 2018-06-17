//app.js
const request = require("./utils/request.js");

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
        apiDomain: "http://demo.wx-dk.cn:8443",
        qiNiuDomain: 'http://img.newlt.justyoujob.com/',
        systemInfo: null
    },
    commonOnShow: function () {

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
        request.post("user/user-info", {}, function (data) {
            that.globalData.user = data.user
            if (success)
                success()
        })
    },
    checkSessionAndLogin: function (success) {
        let that = this
        wx.checkSession({
            success: success,
            fail: function () {
                that.login(success);
            }
        })
    },
    login: function (success) {
        let that = this;
        wx.login({
            success: function (res) {
                if (res.code) {
                    request.post("user/app-login", {code: res.code}, data => {
                        that.globalData.user = data.user;
                        if(success)
                            success();
                    })
                } else {
                    console.log('登录失败：' + res.errMsg)
                }
            },
            fail: function (res) {
                console.log(res);
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
    toast: function (text, icon, callback) {
        icon = icon == undefined ? "none" : icon
        wx.showToast({
            title: text,
            icon: icon,
            complete: callback
        })
    },
    confirm: function (content, success, fail, title, confirmText, cancelText) {
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
                            that.toast("请允许小程序的 "+txt+" 权限","none")
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
    turnPage: function (url,success) {
        if (!url)
            return false
        if(!success)
            success = function () {
                
            }
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
    }
})