const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        picker: false,
        typesData: [],
        pickerValue: [0, 0],
        qTypes: []
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
    register: function (e) {
        let that = this
        console.log(e)
        app.setUserInfo(e.detail, function () {
            that.setData({
                user: app.globalData.user
            })
        });
    },
    picker: function () {
        let that = this
        if (!that.common())
            return true
        that.setData({
            picker: true
        })
    },
    pickerSubmit: function (e) {
        console.log(e)
        let that = this,
            value = e.detail.value,
            tid = value[1]
        app.post("question/change-type", {tid: tid}, function (res) {
            app.globalData.user = res.user
            app.toast("切换分类成功", "success")
            that.setData({
                pickerValue: res.value,
                user: res.user,
                qTypes: res.qTypes
            })
        })
    },
    order: function () {
        let that = this
        if (!that.common())
            return true
        app.turnPage("order/record")
    },
    record: function () {
        let that = this
        if (!that.common())
            return true
        app.turnPage("question/examRecord")
    },
    train: function (e) {
        let that = this,
            index = e.detail.value,
            type = that.data.qTypes[index].type
        if (!that.common())
            return true
        app.get("question/train-last-offset", {type: type}, function (res) {
            res.offset = !res.offset || res.offset == 0 ? 1 : res.offset
            let url = "question/train?tid=" + res.tid + "&type=" + type
            if (res.offset == 1) {
                app.turnPage(url)
                return true
            } else {
                app.confirm("您上次练习到第" + res.offset + "题，需要继续练习吗？", function () {
                    app.turnPage(url + "&offset=" + res.offset)
                }, function () {
                    app.turnPage(url)
                }, "提示",  "重新开始", "继续上次")
            }
        })
    },
    exam: function () {
        let that = this
        if (!that.common())
            return true
        app.get("exam/last", {}, function (re) {
            if (re.exam.eid) {
                let time = parseInt((new Date()).getTime() / 1000),
                    timeStr = app.formatSecondStr(re.exam.expire_at - time)
                app.confirm("您上次的模考还有 " + timeStr + " 结束，需要继续考试吗？", function () {
                    app.turnPage("question/exam?eid=" + re.exam.eid + '&all=1')
                }, function () {
                    that.generateExam()
                }, "提示", "重新开始", "继续考试")
            } else
                that.generateExam()
        })
    },
    generateExam: function () {
        let that = this
        app.post("exam/exam", {}, function (res) {
            if (res.eid && res.eid > 0)
                app.turnPage("question/exam?eid=" + res.eid)
            else
                app.toast("生成考卷失败，请重试");
        })
    },
    common: function () {
        if (!this.data.user.nickname) {
            app.toast("请先注册")
            this.setData({
                user: {}
            })
            return false
        }
        return true
    },
})