const app = getApp()

Page({
    data: {
        tid: 0,
        user: {},
        domain: app.globalData.qiNiuDomain,
        prices: [],
        type: {},
        countIndex: 0,
        picker: false,
        typesData: [],
        pickerValue: [0, 0],
        qTypes: [],
    },
    onLoad: function (options) {
        let tid = options && options.hasOwnProperty("id") ? options.id : 0
        if (tid == 0)
            app.toast("不存在的题库类型", "none", function () {
                wx.navigateBack()
            })
        this.data.tid = tid
    },
    onShow: function () {
        let that = this
        app.getUserInfo(function () {
            that.setData({
                user: app.globalData.user
            })
            app.commonOnShow()
        })
        this.info()
    },
    getPrices: function () {
        let that = this,
            tid = that.data.tid
        app.get("goods/prices", {tid: tid}, function (data) {
            that.setData({
                prices: data.prices,
            })
        })
    },
    order: function (e) {
        let that = this,
            pid = e.currentTarget.dataset.pid,
            index = e.currentTarget.dataset.index,
            obj = that.data.prices[index]
        app.confirm("确定以" + (obj.price / 100) + "元的价格购买" + obj.hourStr + "的使用期？", function () {
            app.post("goods/order", {pid: pid}, function (res) {
                app.turnPage("order/info?id=" + res.oid)
            })
        });
    },
    info: function () {
        let that = this
        app.get("question/info", {tid: that.data.tid}, function (res) {
            that.setData({
                type: res.type,
                typesData: res.types,
            })
            app.setTitle(res.type.name)
            if (!res.type.on)
                that.getPrices()
            else {
                that.data.countIndex++
                that.countDown(that.data.countIndex)
            }
        })
    },
    countDown: function (index) {
        let that = this,
            expire = that.data.type.expire,
            time = parseInt((new Date()).getTime() / 1000)
        if (index != that.data.countIndex)
            return false
        if(index && that.data.countIndex <= 0)
            return true
        if (time >= expire) {
            that.info()
            that.setData({
                "type.timeStr": ""
            })
            return false
        }
        that.setData({
            "type.timeStr": app.formatSecondStr(expire - time)
        })
        setTimeout(() => {
            that.countDown(index)
        }, 1000)
    },
    exam: function (e) {
        let that = this,
            value = e.detail.value,
            tid = that.data.typesData[value].tid
        if (!that.data.type.on) {
            app.toast("您尚未开通此科目，请在下方支付并开通", "none")
            return false
        }
        app.get("exam/last", {tid: tid}, function (re) {
            if (re.exam.eid) {
                let time = parseInt((new Date()).getTime() / 1000),
                    timeStr = app.formatSecondStr(re.exam.expire_at - time)
                app.confirm("您上次的模考还有 " + timeStr + " 结束，需要继续考试吗？", function () {
                    app.turnPage("question/exam?eid=" + re.exam.eid + '&all=1')
                }, function () {
                    that.generateExam()
                }, "提示", "继续考试", "重新开始")
            } else {
                that.generateExam()
            }
        })
    },
    generateExam: function () {
        app.post("exam/exam", {}, function (res) {
            if (res.eid && res.eid > 0)
                app.turnPage("question/exam?eid=" + res.eid)
            else
                app.toast("生成考卷失败，请重试");
        })
    },
    train: function (e) {
        let that = this,
            type = that.data.type,
            list = that.data.typesData
        if (!type.on) {
            app.toast("您尚未开通此科目，请在下方支付并开通", "none")
            return false
        }
        if (list.length <= 0) {
            app.toast("题库内暂无题目", "none")
            return false
        }
        console.log("start")
        that.setData({
            picker: true
        })
    },
    goTrain: function (e) {
        // app.turnPage("question/train")
        let that = this,
            value = e.detail.value,
            tid = value[0],
            t = value[1],
            offset = 1
        app.get("question/train-last-offset", {type: t, tid: tid}, function (res) {
            res.offset = !res.offset || res.offset == 0 ? 1 : res.offset
            let url = "question/train?tid=" + res.tid + "&type=" + t
            if (res.offset == 1) {
                app.turnPage(url)
                return true
            } else {
                app.confirm("您上次练习到第" + res.offset + "题，需要继续练习吗？", function () {
                    app.turnPage(url + "&offset=" + res.offset)
                }, function () {
                    app.turnPage(url)
                }, "提示", "继续上次", "重新开始")
            }
        })
    },
    onUnload: function () {
        this.data.countIndex = -10
    },
})