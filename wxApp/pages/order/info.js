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
    },
    onLoad: function (options) {
        let that = this,
            oid = options && options.hasOwnProperty("id") ? options.id : 0
        if (oid == 0)
            app.toast("不存在的订单", "none", function () {
                wx.navigateBack()
            })
        that.data.oid = oid
        that.getInfo()
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
                wx.hideLoading()
            }
        })
    },
    goPay:function () {
        let that = this,
            oid = that.data.oid,
            data = that.data.pay
        if(!data){
            app.post("order/pay",{oid:oid},function (res) {
                that.data.pay = res.params
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
                    waiting:true
                })
                that.query(1)
            }
        })
    },
    query: function (index) {
        let that = this,
            oid = that.data.oid
        app.post("order/query", {oid: oid}, function (res) {
            that.setData({
                info: res.info
            })
        })
    },
    onUnload: function () {
        this.data.timeOutIndex = -10
        wx.hideLoading()
    }
})