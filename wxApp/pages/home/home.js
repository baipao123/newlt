const app = getApp()

Page({
    data: {
        user: {},
        sliders: [],
        domain:app.globalData.qiNiuDomain,
    },
    onLoad: function () {
        this.getSlider()
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
    getSlider: function () {
        let that = this
        app.get("slider/index", {}, function (res) {
            that.setData({
                sliders: res.list
            })
        })
    }
})