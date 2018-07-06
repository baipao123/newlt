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
        app.get("question/train-last-offset", {type: type}, function (res) {
            res.offset = !res.offset || res.offset == 0 ? 1 : res.offset
            let url = "question/train?tid=" + res.tid + "&type=" + type + "&offset=" + res.offset
            if (res.offset == 1) {
                app.turnPage(url)
                return true
            } else {
                app.confirm("您上次练习到第" + res.offset + "题，需要继续练习吗？", function () {
                    app.turnPage(url)
                },function () {
                    let url = "question/train?tid=" + res.tid + "&type=" + type + "&offset=1"
                    app.turnPage(url)
                })
            }
        })
    },
    exam: function () {
        let that = this
        if (!that.common())
            return true
    },
    common: function () {
        return true
    },
})