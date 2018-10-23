const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        question: {
            type: Object,
            value: {},
            observer: function (newData, oldData) {
                let userAnswer = newData.userAnswer ? newData.userAnswer : '',
                    children = newData.children,
                    ajaxAnswer = [],
                    questionChildren = []
                for (let qid in children) {
                    let child = children[qid]
                    if (child.userAnswer && child.userAnswer != '')
                        ajaxAnswer[child.qid] = child.userAnswer
                    questionChildren.push(child)
                }
                this.setData({
                    userAnswer: userAnswer,
                    ajaxAnswer: ajaxAnswer,
                    questionChildren: questionChildren
                })
            }
        },
        offset: {
            type: Number,
            value: 1,
            observer: function (newData, oldData) {

            }
        },
        type: {
            type: Number,
            value: 1,
            observer: function (newData, oldData) {

            }
        },
        isChild:{
            type: Boolean,
            value: false,
            observer: function (newData, oldData) {

            }
        },
        indexNum:{
            type: Number,
            value: 0,
            observer: function (newData, oldData) {
                console.log(newData)
            }
        }
    },
    data: {
        domain: app.globalData.qiNiuDomain,
        userAnswer: '',
        ajaxAnswer:[],
        questionChildren:[]
    },
    ready: function () {
    },
    methods: {
        chose: function (e) {
            let that = this,
                option = e.currentTarget.dataset.option,
                question = that.data.question,
                offset = that.data.offsets,
                answer = that.data.userAnswer ? that.data.userAnswer.split("") : [],
                newUserAnswer = ""
            if (question.answer || question.type == 4 || question.type == 5)
                return true

            if (question.type <= 2) {
                that.answer({
                    qid: question.qid,
                    offset: offset,
                    answer: option
                })
                newUserAnswer = option
            } else if(question.type == 3){
                let index = answer.indexOf(option)
                if (index > -1)
                    answer.splice(index, 1)
                else
                    answer.push(option)
                answer.sort()
                newUserAnswer = answer.join("")
            }
            that.afterFill(newUserAnswer)
        },
        fillBlank:function (e) {
            let that = this,
                userAnswer = e.detail.value
            that.data.ajaxAnswer[that.data.question.qid] = userAnswer
            that.afterFill(userAnswer)
        },
        afterFill:function (userAnswer) {
            let that = this
            that.setData({
                userAnswer: userAnswer
            })
            if (that.data.isChild)
                that.triggerEvent('Chose', {"qid": that.data.question.qid, "userAnswer": userAnswer,index:that.data.indexNum})
            else if(that.data.type == 1)
                that.goAnswer()
        },
        goAnswer: function (e) {
            let that = this,
                data = {
                    offset: that.data.offset,
                    answer: that.data.ajaxAnswer
                }
            if (data.answer.length == 0)
                app.toast(that.question.type == 4 ? "请先填写答案" : "请先选择答案")
            else {
                app.post(type == 1 ? "question/answer" : "exam/answer", data, function (res) {
                    that.setData({
                        question: res.info
                    })
                    that.triggerEvent('afterAnswer', {question: res.info, offset: data.offset})
                })
            }
        },
        seeAnswer: function (e) {
            let that = this,
                qid = that.data.question.qid,
                offset = that.data.offset
            if(that.data.question.hasAnswer)
                return true
            app.confirm("确定直接查看答案？", function () {
                that.answer({qid: qid, offset: offset})
            })
        },
        childChose: function (data) {
            let that = this,
                qid = data.qid
            that.data.question.children[qid].userAnswer = data.userAnswer
            that.data.ajaxAnswer[qid] = data.userAnswer
            that.data.questionChildren[data.index - 1].userAnswer = data.userAnswer
            that.setData({
                "questionChildren": that.data.questionChildren
            })
        }
    }
})