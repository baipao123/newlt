const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        questions: {
            type: Object,
            value: {},
            observer: function (newData, oldData) {
                if (!app.isEmptyObject(this.data.questions)) {
                    for (let index in newData) {
                        oldData[index] = newData[index]
                    }
                    this.data.questions = oldData
                    this.setOffset()
                }
            }
        },
        offset: {
            type: Number,
            value: 0,
            observer: function (newData, oldData) {
                this.setData({
                    offset: newData
                })
                if(oldData == 0 && !app.isEmptyObject(this.data.questions))
                    this.setOffset()
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
        info: {
            type: Object,
            value: 1,
            observer: function (newData, oldData) {

            }
        },
    },
    data: {
        domain: app.globalData.qiNiuDomain,
        question:{},
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
        getMore: function (offset) {
            this.data.loading = true
            this.triggerEvent('MoreList', {offset: offset})
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
                })
                that.data.loading = false
            } else {
                that.setData({
                    question:{},
                    offset: offset,
                })
                that.getMore(prev ? Math.max(offset - 9, 1) : offset,prev ? offset : 0)
            }
        },
        afterAnswer:function (e) {
            let that = this,
                data = e.detail,
                question = data.question,
                qid = question.qid,
                offset = that.data.offset
            that.data.questions[offset] = question
        }
    }
})