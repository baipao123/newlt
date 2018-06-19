const app = getApp()

Page({
    data: {
        user: {},
        sliders: [],
        empty: false,
        loading: false,
        refresh: false,
        page: 1
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
    },
    getList: function (page, refresh) {

    },
    onReachBottom: function () {
        if (this.data.empty || this.data.loading)
            return true;
        let that = this
        that.getList(that.data.page, false)
    },
    onPullDownRefresh: function () {
        let that = this
        if (that.data.refresh)
            return true
        that.getList(1, true)
        that.getSlider()
    },
})