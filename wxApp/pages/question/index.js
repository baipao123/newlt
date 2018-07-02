const app = getApp()

Page({
    data: {
        tid: 0,
        user: {},
        domain: app.globalData.qiNiuDomain,
        prices: [],
        type: {},
        countIndex: 0,
        pickerShow: false,
        types: [],
        typesChild: [],
        pickerValue: [0, 0],
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
                app.turnPage("order/pay?id=" + res.oid)
            })
        });
    },
    info: function () {
        let that = this
        app.get("question/info", {tid: that.data.tid}, function (res) {
            that.setData({
                type: res.type,
                types: res.types,
                typesChild: res.types[0].child
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
        let that = this
        if (!that.data.type.on) {
            app.toast("请先购买题库", "none")
            return false
        }
    },
    train: function (e) {
        let that = this,
            type = that.data.type,
            list = that.data.types
        if (!type.on) {
            app.toast("请先购买题库", "none")
            return false
        }
        if (list.length <= 0) {
            app.toast("题库内暂无题目", "none")
            return false
        }
        that.setData({
            pickerShow: true
        })
    },
    goTrain: function (tid) {
        app.turnPage("question/train")
    },
    pickerChange:function (e) {
        let that = this,
            oldValue = that.data.pickerValue,
            value = e.detail.value,
            typeIndex = value[0],
            childIndex = value[1],
            type = that.data.types[typeIndex],
            children = type.child
        if (oldValue[0] != typeIndex) {
            that.setData({
                pickerValue: value,
                typesChild: children
            })
        } else if (oldValue[1] != childIndex) {
            that.setData({
                pickerValue: value,
            })
        }
    }
})