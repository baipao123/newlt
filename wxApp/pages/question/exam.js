const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        eid: 0,
        exam: {},
        qNum: [],
        questions: {},
        question: {},
        type: 0,   // 题型
        offset: 1,
        showIndex: false,
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
        that.getList()
        that.getInfo()
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
    setQuestion: function (again) {
        let that = this,
            type = that.data.type,
            offset = that.data.offset,
            questions = that.data.questions
        if (questions[type] && questions[type][offset]) {
            console.log(questions[type][offset])
            that.setData({
                question: questions[type][offset],
                type: type,
                offset: offset
            })
        } else if (again) {
            app.toast("没有题目了")
        } else
            that.getList()
    },
    getList: function () {
        let that = this,
            data = {
                eid: that.data.eid,
                type: that.data.type,
                offset: that.data.offset
            }
        app.post("exam/list", data, function (res) {
            if (res.list.length == 0)
                app.toast("没有更多题目了")
            else {
                let questions = that.data.questions
                console.log(res.list)
                for (let type in res.list) {
                    for (let offset in res.list[type]) {
                        if (!questions[type])
                            questions[type] = {}
                        questions[type][offset] = res.list[type][offset]
                    }
                }
                that.data.questions = questions
                that.setQuestion(true)
            }
        })
    },
    chose:function (e) {
        let that = this,
            option = e.currentTarget.dataset.option
    },
    goAnswer:function (e) {

    },
    answer:function (data) {

    },
    getInfo: function () {
        let that = this
        app.post("exam/info", {eid: that.data.eid}, function (res) {
            that.setData({
                exam: res.exam,
                qNum: res.qNum
            })
            that.timeStr(0)
        })
    },
    timeStr: function (index) {
        let that = this
        if(index != that.data.timeStrIndex)
            return false
        if (that.data.exam) {
            if (that.data.exam.status == 0) {
                let expire = that.data.exam.expire_at,
                    nowTime = parseInt((new Date()).getTime() / 1000)
                if (expire > nowTime) {
                    console.log(expire-nowTime)
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
        console.log(123)
        that.data.exam.status = 1
    },
    finish: function () {
        let that = this,
            total = that.data.questions.length,
            num = that.data.answers.length
        app.confirm("共有试题" + total + "题，已做" + num + "题，确定交卷吗？", function () {
            that.finishExam()
        }, function () {

        }, "交卷", "确定交卷", "检查一下")
    },
    examIndex: function () {
        this.setData({
            showIndex: !this.data.showIndex
        })
    },
    goSingle:function (e) {
        let that = this,
            type = e.currentTarget.dataset.type,
            offset = e.currentTarget.dataset.offset
        that.setData({
            showIndex: false
        })
        that.data.type = type,
        that.data.offset = offset
        that.setQuestion()
    },
    empty:function () {
        
    },
})