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
    register:function (e) {
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
        let that = this,
            tid = e.detail.tid
        app.post("question/change-type",{tid:tid},function (res) {
            app.globalData.user = res.user
            that.setData({
                pickerValue:res.value,
                user:res.user
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
    exam: function () {
        let that = this
        if (!that.common())
            return true
    },
    common: function () {
        return true
    },
})