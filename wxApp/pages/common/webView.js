const app = getApp()

Page({
    data: {
        user: {},
        link: ""
    },
    onLoad: function (options) {
        let url = options && options.hasOwnProperty("url") ? options.url : ""
        if (url == "") {
            wx.navigateBack()
        } else {
            this.setData({
                link: url
            })
        }
        wx.hideShareMenu()
    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user: app.globalData.user
            })
            app.commonOnShow()
        })
    }
})