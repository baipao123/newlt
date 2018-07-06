const app = getApp()

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        picker: false,
        typesData: [],
        pickerValue: [0, 0],
    },
    onLoad: function () {
        let that = this
        app.get("question/all-types", {}, function (res) {
            that.setData({
                typesData: res.types,
                pickerValue: res.value
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
                user: res.user
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
    train: function () {
        let that = this
        app.get("question/train-last-offset", {}, function (res) {
            let url = "question/train?tid=" + res.tid + "&type=" + res.type + "&offset=" + res.offset
            if (res.offset == 0) {
                app.turnPage(url)
                return true
            } else {
                app.confirm(res.text, function () {
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