const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
    },
    onLoad: function () {

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
    call: function (e) {
        let phone = e.currentTarget.dataset.phone
        wx.makePhoneCall({
            phoneNumber: phone
        })
    },
    preview: function (e) {
        let that = this,
            url = e.currentTarget.dataset.url
        wx.previewImage({
            current: url, // 当前显示图片的http链接
            urls: [url] // 需要预览的图片http链接列表
        })
    }
})