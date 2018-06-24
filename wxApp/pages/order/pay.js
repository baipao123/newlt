const app = getApp()

Page({
    data: {
        oid: 0,
        user: {},
        domain: app.globalData.qiNiuDomain,
    },
    onLoad: function (options) {
        let oid = options && options.hasOwnProperty("id") ? options.id : 0
        if (oid == 0)
            app.toast("不存在的订单", "none", function () {
                wx.navigateBack()
            })
        this.data.oid = oid
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
    getInfo: function () {
        let that = this
        app.get("order/info-for-pay",{oid:that.data.oid},function (res) {

        })
    },
    getPayInfo: function () {

    },
    wxPay: function () {

    }
})