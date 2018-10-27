const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        eid: 0,
        exam: {},
        qNum: [],
        questions: {},
        offset: 1,
        showIndex: false,
        doneNum: 0,
        timeStrIndex: 0, // 倒计时
    },
    onLoad: function (options) {
        let that = this,
            eid = options.hasOwnProperty("eid") ? options.eid : 0,
            all = options.hasOwnProperty("all") ? options.all : 0
        if (eid <= 0) {
            app.toast("未知考卷", "none", function () {
                wx.navigateBack()
            })
        }
        if (all > 0)
            that.setData({showIndex: all > 0})
        that.data.eid = eid
        that.getInfo(eid)
        that.getList({
            eid: eid,
            offset: 1
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
    },
    timeStr: function (index) {
        let that = this
        if (index != that.data.timeStrIndex)
            return false
        if(index && that.data.timeStrIndex <= 0)
            return true
        if (that.data.exam) {
            if (that.data.exam.status == 0) {
                let expire = that.data.exam.expire_at,
                    nowTime = parseInt((new Date()).getTime() / 1000)
                if (expire > nowTime) {
                    console.log(expire - nowTime)
                    that.setData({
                        "exam.timeStr": app.formatSecondStr(expire - nowTime)
                    })
                } else {
                    that.data.exam.status == 1
                    app.confirm("时间到，请问是否交卷?", function () {
                        that.finishExam()
                    }, function () {
                        app.confirm("确定放弃交卷？放弃后无法查看考试记录", function () {
                            wx.navigateBack()
                        }, function () {
                            that.finishExam()
                        }, "提示", "确定", "交卷")

                    }, "提示", "交卷", "放弃")
                    return true
                }
            } else
                return true
        }
        that.data.timeStrIndex++
        setTimeout(() => {
            that.timeStr(that.data.timeStrIndex)
        }, 1000)
    },
    finishExam: function () {
        let that = this
        if (that.data.exam.status != 0)
            return true;
        that.data.exam.status = 1
        app.post("exam/finish", {eid: that.data.eid}, function (res) {
            app.alert("交卷成功，得分：" + res.score + '分', function () {
                wx.redirectTo({
                    url: "/pages/question/exam?eid=" + that.data.eid + "&all=1"
                })
            })
        },function (res) {
            that.data.exam.status = 0
        })
    },
    finish: function () {
        let that = this,
            total = that.data.exam.totalNum
        if(that.data.exam.status != 0)
            return false
        that.getInfo(that.data.eid,true,function () {
            let num = that.data.doneNum
            app.confirm("共有试题" + total + "小题，已做" + num + "小题，确定交卷吗？", function () {
                that.finishExam()
            }, function () {

            }, "交卷", "确定交卷", "检查一下")
        })
    },
    examIndex: function () {
        if (!this.data.showIndex)
            this.getInfo(this.data.eid, true)
        this.setData({
            showIndex: !this.data.showIndex
        })
    },
    goSingle: function (e) {
        let that = this,
            offset = e.currentTarget.dataset.offset
        that.data.offset = offset
        that.setData({
            showIndex: false,
            offset: offset
        })
    },
    empty: function () {

    },
    moreList: function (e) {
        let that = this,
            data = e.detail,
            obj = {
                eid: that.data.eid,
                offset: data.offset
            }
        that.getList(obj)
    },
    getList: function (data) {
        let that = this
        app.post("exam/list", data, function (res) {
            if (res.list.length == 0)
                app.toast("没有更多题目了")
            else
                that.setData({
                    questions: res.list
                })
        })
    },
    getInfo: function (eid,again,success) {
        let that = this
        app.post("exam/info", {eid: eid}, function (res) {
            that.setData({
                eid: eid,
                exam: res.exam,
                qNum: res.qNum,
                maxOffset: res.num,
                doneNum: res.doneNum
            })
            that.data.doneNum = res.doneNum
            if (!again)
                that.timeStr(0)
            if(success)
                success()
        })
    },
    onUnload: function () {
        this.data.timeStrIndex = -10
    },
})