const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        questions: [],
        tid: 0,
        offset: 1,
        maxOffset: 100,
        needOffset : 0,
        info:{}
    },
    onLoad: function (options) {
        let that = this,
            tid = options.hasOwnProperty("tid") ? options.tid : 0,
            offset = options.hasOwnProperty("offset") ? options.offset : 1
        if (tid <= 0) {
            app.toast("未知题库", "none", function () {
                wx.navigateBack()
            })
        }
        that.data.tid = tid
        that.data.offset = offset
        that.getList()
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
    moreList: function (e) {
        let that = this
        that.data.offset = e.detail.offset
        that.data.needOffset = e.detail.needOffset
        that.getList()
    },
    getList: function () {
        let that = this,
            data = {
                tid: that.data.tid,
                offset: that.data.offset
            }
        app.get("question/train-list", data, function (res) {
            if (res.list.length == 0)
                app.toast("没有更多题目了")
            else
                that.setData({
                    questions: res.list,
                    offset: that.data.needOffset > 0 ? that.data.needOffset :  that.data.offset,
                    maxOffset: res.num,
                    title: res.title,
                })
        })
    }
})