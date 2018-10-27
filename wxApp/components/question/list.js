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
                this.data.index = newData
                this.setData({
                    index:newData
                })
                if (!app.isEmptyObject(this.data.questions))
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
        eid: {
            type: Number,
            value: 0,
            observer: function (newData, oldData) {
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
        index:0
    },
    ready: function () {
    },
    methods: {
        prev: function () {
            if(this.data.loading)
                return true
            this.data.index--
            this.setOffset(true)
        },
        next: function () {
            if(this.data.loading)
                return true
            if (!this.data.questions[this.data.index + 1] && this.data.index >= this.data.maxOffset) {
                app.toast("已到最后一题")
                return true
            }
            this.data.index++
            this.setOffset()
        },
        getMore: function (offset) {
            this.data.loading = true
            this.triggerEvent('MoreList', {offset: offset})
        },
        setOffset: function (prev) {
            let that = this,
                index = that.data.index,
                questions = that.data.questions
            console.log(index)
            if (index <= 0) {
                app.toast("已经是第一题了", "none")
                return false
            }
            if (questions[index]) {
                that.setData({
                    question: questions[index],
                    index: index,
                })
                that.data.loading = false
            } else {
                that.setData({
                    question:{},
                    index: index,
                })
                that.getMore(prev ? Math.max(index - 9, 1) : index,prev ? index : 0)
            }
        },
        afterAnswer:function (e) {
            let that = this,
                data = e.detail,
                question = data.question,
                qid = question.qid,
                index = that.data.index
            that.data.questions[index] = question
        }
    }
})