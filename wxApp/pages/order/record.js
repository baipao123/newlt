const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        list: [],
        page: 1,
        refresh: false,
        loading: false,
        empty: false,
        nowTime: 0,
        timeOutIndex: 1,
    },
    onLoad: function () {
        this.getList()
    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user: app.globalData.user
            })
            app.commonOnShow()
        })
        that.data.timeOutIndex = 1
        that.countDown(1)
    },
    countDown: function (index) {
        let that = this
        if (index && index != that.data.timeOutIndex)
            return true
        if (index && that.data.timeOutIndex <= 0)
            return true
        let time = parseInt((new Date()).getTime() / 1000)
        console.log(time)
        that.setData({
            nowTime: time
        })
        that.data.timeOutIndex++
        setTimeout(() => {
            that.countDown(that.data.timeOutIndex)
        }, 1000)
    },
    getList: function (refresh) {
        let that = this,
            page = that.data.page
        that.data.loading = true
        that.data.refresh = !!refresh
        that.setData({
            loading: true
        })
        app.get("order/record", {page: page}, function (res) {
            if (page == 1) {
                that.setData({
                    list: res.list,
                    loading: false,
                    empty: res.list.length == 0
                })
            } else {
                let list = that.data.list
                list.push.apply(res.list)
                that.setData({
                    list: list,
                    loading: false,
                    empty: res.list.length == 0
                })
            }
            that.data.loading = false
            that.data.refresh = false
        })
    },
    order: function (e) {
        let oid = e.currentTarget.dataset.oid
        app.turnPage("order/info?id=" + oid)
    },
    onPullDownRefresh: function () {
        let that = this
        if (that.data.refresh)
            return false
        that.data.page = 1
        that.getList(true)
    },
    onReachBottom: function () {
        let that = this
        if (that.data.loading || that.data.empty)
            return false
        that.data.page++
        that.getList()
    },
    onUnload: function () {
        this.data.timeOutIndex = -10
    },
})