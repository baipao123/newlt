const app = getApp()

Page({
    data: {
        tid: 0,
        user: {},
        domain:app.globalData.qiNiuDomain,
        prices: [],
        type: {},
        isBuy: false
    },
    onLoad: function (options) {
        let tid = options && options.hasOwnProperty("id") ? options.id : 0
        if (tid == 0)
            app.toast("不存在的题库类型", "none", function () {
                wx.navigateBack()
            })
        this.data.tid = tid
    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user: app.globalData.user
            })
            that.getPrices()
            app.commonOnShow()
        })
    },
    getPrices: function () {
        let that = this,
            tid = that.data.tid
        app.get("goods/prices", {tid: tid}, function (data) {
            that.setData({
                prices: data.prices,
                type: data.type
            })
            app.setTitle(data.type.name)
        })
    },
    order: function (e) {
        let that = this,
            pid = e.currentTarget.dataset.pid,
            index = e.currentTarget.dataset.index,
            obj = that.data.prices[index]
        app.confirm("确定以" + (obj.price / 100) + "元的价格购买" + obj.hourStr + "的使用期？", function () {
            app.post("goods/order", {pid: pid}, function (res) {
                app.turnPage("order/pay?id=" + res.oid)
            })
        });
    }
})