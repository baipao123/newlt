const app = getApp()

Page({
    data: {
        user: {}
    },
    onLoad: function () {

    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user:app.globalData.user
            })
            app.commonOnShow()
        })
    }
})