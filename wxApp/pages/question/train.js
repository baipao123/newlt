const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        tid: 0,
        type: 0,
        offset: 0
    },
    onLoad: function (options) {
        let that = this,
            tid = options.hasOwnProperty("tid") ? options.tid : 0,
            type = options.hasOwnProperty("type") ? options.type : 0,
            offset = options.hasOwnProperty("offset") ? options.offset : 0
        if (tid <= 0 || type <= 0) {
            app.toast("未知题库", "none", function () {
                wx.navigateBack()
            })
        }
        that.setData({
            tid: tid,
            type: type,
            offset: offset
        })
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