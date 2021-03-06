const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        list: {
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
        },
        maxOffset: {
            type: Number,
            value: 100,
            observer: function (newData, oldData) {
                this.setData({
                    maxOffset: newData
                })
            }
        },
    },
    data: {
        domain: app.globalData.qiNiuDomain,
        questions:[],
        question: {},
        result: {},
        answer: [],
        loading: false,

    },
    ready: function () {
    },
    methods: {
        prev: function () {
            if(this.data.loading)
                return true
            this.data.offset--
            this.setOffset(true)
        },
        next: function () {
            if(this.data.loading)
                return true
            if (!this.data.questions[this.data.offset + 1] && this.data.offset >= this.data.maxOffset) {
                app.toast("已到最后一题")
                return true
            }
            this.data.offset++
            this.setOffset()
        },
        getMore: function (offset,needOffset) {
            this.data.loading = true
            this.triggerEvent('MoreList', {offset: offset, needOffset: needOffset})
        },
        setOffset: function (prev) {
            let that = this,
                offset = that.data.offset,
                questions = that.data.questions
            console.log(offset)
            if (offset <= 0) {
                app.toast("已经是第一题了", "none")
                return false
            }
            if (questions[offset]) {
                that.setData({
                    question: questions[offset],
                    offset: offset,
                    result: {},
                    answer: [],
                })
                that.data.loading = false
            } else {
                that.setData({
                    question: {},
                    offset: offset,
                    result: {},
                    answer: [],
                })
                that.getMore(prev ? Math.max(offset - 9, 1) : offset,prev ? offset : 0)
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
                let index = answer.indexOf(option)
                if (index > -1)
                    answer.splice(index, 1)
                else
                    answer.push(option)
                that.setData({
                    answer: answer
                })
            }
        },
        goAnswer: function (e) {
            let that = this,
                data = {
                    qid: that.data.question.qid,
                    offset: that.data.offset,
                    answer: that.data.answer
                }
            if (data.answer.length == 0)
                app.toast("请先选择答案")
            else
                that.answer(data)
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