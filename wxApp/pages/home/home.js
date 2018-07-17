const app = getApp()

Page({
    data: {
        user: {},
        sliders: [],
        types:[],
        domain:app.globalData.qiNiuDomain,
    },
    onLoad: function () {
        this.getSlider()
        this.getTypes()
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
    getTypes: function () {
        let that = this
        app.get("goods/types", {}, function (res) {
            that.setData({
                types: res.types
            })
        })
    },
    play: function (e) {
        let that = this,
            video = wx.createVideoContext("video")
        video.play()
        console.log(1)
        video.requestFullScreen()
    },
    exitPlay: function (e) {
        let that = this
        console.log(e)
        if(!e.detail.fullScreen) {
            wx.createVideoContext("video").pause()
        }
    }
})