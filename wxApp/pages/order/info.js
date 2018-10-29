const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        oid: 0,
        info: {},
        pay: {},
        waiting: false,
        timeOutIndex: 1,
        nowTime: 0,
        back: 0
    },
    onLoad: function (options) {
        let that = this,
            oid = options && options.hasOwnProperty("id") ? options.id : 0,
            back = options && options.hasOwnProperty("back") ? options.back : 0
        if (oid == 0)
            app.toast("不存在的订单", "none", function () {
                wx.navigateBack()
            })
        that.data.oid = oid
        that.data.back = back
        that.getInfo()
        that.countDown()
    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user: app.globalData.user
            })
            app.commonOnShow()
        })
    },
    getInfo: function (index) {
        let that = this,
            oid = that.data.oid
        app.post("order/info", {oid: oid}, function (res) {
            that.setData({
                info: res.info
            })
            console.log(res)
            if (res.info.status == 10 || res.info.status == 11) {
                if (!index || index != that.data.timeOutIndex || that.data.timeOutIndex < 0)
                    return true
                wx.showLoading({
                    title: "查询支付结果中"
                })
                that.data.timeOutIndex++
                setTimeout(() => {
                    that.getInfo(that.data.timeOutIndex)
                }, 500 + Math.ceil(Math.random() * 1000))
            } else {
                that.setData({
                    waiting: false
                })
                if (res.info.status == 20 && res.user)
                    app.globalData.user = res.user
                wx.hideLoading()
                if (that.data.back > 0)
                    wx.navigateBack()
            }
        })
    },
    goPay:function (e) {
        let that = this,
            oid = that.data.oid,
            data = that.data.pay,
            formId = e.detail.formId
        if(!data.timeStamp){
            app.post("order/pay",{oid:oid,formId:formId},function (res) {
                that.data.pay = res.params
                that.pay()
            })
        }else
            return that.pay()
    },
    pay:function () {
        let that = this,
            data = that.data.pay
        if(!data)
            return that.goPay()
        wx.requestPayment({
            timeStamp: data.timeStamp,
            nonceStr: data.nonceStr,
            package: data.package,
            signType: data.signType,
            paySign: data.paySign,
            success: function (res) {
                that.setData({
                    "info.status": 11,
                    waiting: true
                })
                that.data.countIndex = 1
                that.getInfo(1)
            }
        })
    },
    query: function () {
        let that = this,
            oid = that.data.oid
        app.post("order/query", {oid: oid}, function (res) {
            that.setData({
                info: res.info
            })
        })
    },
    countDown: function (index) {
        let that = this
        if ((index && index != that.data.timeOutIndex) || that.data.timeOutIndex <= 0)
            return true
        let time = parseInt((new Date()).getTime() / 1000)
        console.log(time)
        that.setData({
            nowTime: time
        })
        if (that.data.info.status && (that.data.info.status != 1 || time >= that.data.info.expire_at))
            return true
        that.data.timeOutIndex++
        setTimeout(() => {
            that.countDown(that.data.timeOutIndex)
        }, 1000)
    },
    onHide:function () {
        this.data.timeOutIndex = -10
        wx.hideLoading()
    },
    onUnload: function () {
        this.data.timeOutIndex = -10
        wx.hideLoading()
    }
})