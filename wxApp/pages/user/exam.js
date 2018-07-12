const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        nowTime: 0,
        timeOutIndex : 1,
        picker: false,
        typesData: [],
        pickerValue: [0, 0],
        qTypes: [],
        refresh: false,
        loading: false,
        empty: false,
        page: 1,
        tid: 0
    },
    onLoad: function () {
        let that = this
        app.get("question/all-types", {}, function (res) {
            that.setData({
                typesData: res.types,
                pickerValue: res.value,
                qTypes: res.qTypes
            })
        })
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
        that.countDown()
    },
    countDown:function (index) {
        let that = this
        console.log(index)
        console.log(that.data.timeOutIndex)
        if(index && index != that.data.timeOutIndex)
            return true
        if(index && that.data.timeOutIndex <= 0)
            return true
        let time = parseInt((new Date()).getTime() / 1000)
        that.setData({
            nowTime: time
        })
        that.data.timeOutIndex++
        setTimeout(()=>{
            that.countDown(that.data.timeOutIndex)
        } , 1000)
    },
    picker: function () {
        let that = this
        that.setData({
            picker: true
        })
    },
    pickerSubmit: function (e) {
        console.log(e)
        let that = this,
            value = e.detail.value
        that.data.tid = value[1]
        that.data.page = 1
        that.getList(true)
    },
    getList: function (refresh) {
        let that = this,
            data = {
                tid: that.data.tid,
                page: that.data.page
            }
        that.data.loading = true
        that.data.refresh = !!refresh
        that.setData({
            loading: true
        })
        app.get("exam/records", data, function (res) {
            if (res.list.length == 0) {
                that.setData({
                    empty: true,
                    loading: false
                })
            }
            if (that.data.page == 1) {
                that.setData({
                    list: res.list,
                    info: res.info,
                    loading: false
                })
            } else {
                let list = that.data.list
                list.push.apply(res.list)
                that.setData({
                    list: list,
                    loading: false
                })
            }
            that.data.loading = false
            that.data.refresh = false
        })
    },
    goRecord: function (e) {
        let that = this,
            eid = e.currentTarget.dataset.eid
        app.turnPage("question/exam?eid=" + eid + "&all=1")
    },
    onPullDownRefresh:function () {
        let that = this
        if(that.data.refresh)
            return false
        that.data.page = 1
        that.getList(true)
    },
    onReachBottom:function () {
        let that = this
        if(that.data.loading || that.data.empty)
            return false
        that.data.page++
        that.getList()
    },
    onUnload: function () {
        this.data.timeOutIndex = -10
    },
})