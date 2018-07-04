const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        questions: {
            type: Object,
            value: [],
            observer: function (newData, oldData) {
                for (let index in newData) {
                    this.data.questions[index] = newData[index]
                }
                this.setOffset()
            }
        },
        offset: {
            type: Number,
            value: 1,
            observer: function (newData, oldData) {
                this.setData({
                    offset: newData
                })
            }
        }
    },
    data: {
        windowWidth: 300,
        domain: app.globalData.qiNiuDomain,
        question: {},
        result: {},
        answer: []
    },
    ready: function () {
        let that = this
        app.getSystemInfo(function (info) {
            that.setData({
                windowWidth: info.windowWidth
            })
        })
        console.log(123)
    },
    methods: {
        prev: function () {
            this.data.offset--
            this.setOffset(true)
        },
        next: function () {
            this.data.offset++
            this.setOffset()
        },
        getMore: function (offset) {
            this.triggerEvent('MoreList', {offset: offset})
        },
        setOffset: function (prev) {
            let that = this,
                offset = that.data.offset,
                questions = that.data.questions
            if (offset <= 0) {
                app.toast("已经是第一题了", "none")
                return false
            }

            if (questions[offset]) {
                that.setData({
                    question: questions[offset],
                    offset: offset,
                    result: {}
                })
                console.log(questions[offset])
            } else {
                that.getMore(prev ? offset - 9 : offset)
            }
        },
        chose: function (e) {
            let that = this,
                option = e.currentTarget.dataset.option,
                question = that.data.question,
                offset = that.data.offset,
                answer = that.data.answer
            if(that.data.result.answer)
                return true
            if (question.type <= 2) {
                that.answer({
                    qid: question.qid,
                    offset: offset,
                    answer: option
                })
                that.setData({
                    answer: [option]
                })
            } else {
                if (answer.indexOf(option) > -1)
                    answer.pop(option)
                else
                    answer.push(option)
                that.setData({
                    answer: answer
                })
            }
        },
        answer: function (data) {
            let that = this
            app.post("question/answer", data, function (res) {
                that.setData({
                    result: res.result
                })
            })
        },
        see: function (e) {
            let that = this,
                offset = that.data.offset,
                qid = that.data.question.qid
            if(that.data.result.answer)
                return true
            app.confirm("确定直接查看答案？", function () {
                that.answer({qid: qid, offset: offset})
            })
        },

    }
})