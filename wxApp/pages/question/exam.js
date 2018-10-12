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
        firstOffset: false, // 是否第一题
        lastOffset: false, // 是否最后一题
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
    isFirstOrLast: function () {
        let that = this,
            offset = that.data.offset,
            type = that.data.type,
            qNum = that.data.qNum,
            typeKeys = Object.keys(qNum)
        if (offset == 1 && typeKeys[0] == type) {
            that.setData({
                firstOffset: true,
                lastOffset: false,
            })
            return true
        } else if (offset == qNum[type].length && typeKeys[typeKeys.length - 1] == type) {
            that.setData({
                firstOffset: false,
                lastOffset: true
            })
            return true
        }
        that.setData({
            firstOffset: false,
            lastOffset: false
        })
    },
    prev: function () {
        let that = this,
            offset = that.data.offset,
            type = that.data.type,
            qNum = that.data.qNum
        if (that.data.firstOffset)
            return true
        if (offset > 1) {
            that.data.offset--
            that.setQuestion(true)
        } else {
            let prevType = 0
            for (let t in qNum) {
                if (t == type) {
                    console.log(prevType)
                    that.data.type = prevType
                    that.data.offset = Object.keys(qNum[prevType]).length
                    that.setQuestion(true)
                    return true
                }
                prevType = t
            }
            app.toast("已经是第一题了")
        }
    },
    next: function () {
        let that = this,
            offset = that.data.offset,
            type = that.data.type,
            qNum = that.data.qNum
        if (that.data.lastOffset)
            return true
        if (offset < Object.keys(qNum[type]).length) {
            that.data.offset++
            that.setQuestion()
        } else {
            let tmp = false
            for (let t in qNum) {
                if (tmp) {
                    that.data.type = t
                    that.data.offset = 1
                    that.setQuestion()
                    return true
                }
                if (t == type)
                    tmp = true
            }
            app.toast("最后一题了")
        }
    },
    setQuestion: function (isPrev, again) {
        let that = this,
            type = that.data.type,
            offset = that.data.offset,
            questions = that.data.questions,
            question
        if (questions[type] && questions[type][offset]) {
            question = questions[type][offset]
            question.user = that.data.qNum[type] && that.data.qNum[type][offset] ? that.data.qNum[type][offset] : {}
            console.log(question)
            that.setData({
                question: question,
                type: type,
                offset: offset
            })
            that.isFirstOrLast()
        } else if (again) {
            app.toast("没有题目了")
        } else
            that.getList(isPrev)
    },
    getList: function (isPrev) {
        let that = this,
            data = {
                eid: that.data.eid,
                type: that.data.type,
                offset: isPrev ? Math.max(1, that.data.offset - 9) : that.data.offset
            }
        app.post("exam/list", data, function (res) {
            if (res.list.length == 0)
                app.toast("没有更多题目了")
            else {
                let questions = that.data.questions
                for (let type in res.list) {
                    for (let offset in res.list[type]) {
                        if (!questions[type])
                            questions[type] = {}
                        questions[type][offset] = res.list[type][offset]
                    }
                }
                that.data.questions = questions
                that.setQuestion(isPrev, true)
            }
        })
    },
    chose: function (e) {
        let that = this,
            option = e.currentTarget.dataset.option,
            qNum = that.data.qNum,
            type = that.data.type
        if (that.data.exam.status != 0)
            return true;
        if (type != 3) {
            let data = {
                eid: that.data.eid,
                qid: that.data.question.qid,
                answer: option
            }
            that.answer(data)
        } else {
            let offset = that.data.offset,
                question = that.data.question,
                uA = question.user.uA ? question.user.uA.split('') : [],
                index = uA.indexOf(option)
            if (index > -1)
                uA.splice(index, 1)
            else
                uA.push(option)
            uA.sort()
            let userAnswer = uA.join("")
            console.log(userAnswer)
            that.setData({
                "question.user.uA": userAnswer
            })
        }
    },
    goAnswer: function (e) {
        let that = this,
            data = {
                eid: that.data.eid,
                qid: that.data.question.qid,
                answer: that.data.question.user.uA
            }
        if (that.data.exam.status != 0)
            return true;
        if (that.data.type != 3)
            return true
        that.answer(data)
    },
    answer: function (data) {
        let that = this
        if (that.data.exam.status != 0)
            return true;
        app.post("exam/answer", data, function (res) {
            let qNum = that.data.qNum,
                type = that.data.type,
                offset = that.data.offset
            qNum[type][offset] = res.user
            that.setData({
                "question.user": res.user,
                qNum: qNum
            })
            if (res.isNew)
                that.data.exam.alNum++
        })
    },
    getInfo: function () {
        let that = this
        app.post("exam/info", {eid: that.data.eid}, function (res) {
            res.exam.alNum = res.alNum
            that.setData({
                exam: res.exam,
                qNum: res.qNum
            })
            for (let t in res.qNum) {
                that.data.type = t
                that.getList()
                that.timeStr(0)
                return true
            }
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
            app.alert("交卷成功，得分：" + res.exam.score + '分', function () {
                wx.redirectTo({
                    url: "/pages/question/exam?eid=" + that.data.eid + "&all=1"
                })
            })
        })
    },
    finish: function () {
        let that = this,
            total = that.data.exam.total,
            num = that.data.exam.alNum
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
    goSingle: function (e) {
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
    empty: function () {

    },
    onUnload: function () {
        this.data.timeStrIndex = -10
    },
})