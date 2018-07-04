const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        urls: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
                this.setData({
                    urls: newData
                })
            }
        },
        src: {
            type: String,
            value: "",
            observer: function (newData, olddata) {
                this.setData({
                    urls: [newData]
                })
            }
        },
        width: {
            type: Number,
            value: 0,
            observer: function (newData, olddata) {
                this.setData({
                    width: newData
                })
            }
        },
        height: {
            type: Number,
            value: 100,
            observer: function (newData, olddata) {
                this.setData({
                    height: newData
                })
            }
        },
        ext: {
            type: String,
            value: "",
            observer: function (newData, olddata) {
                this.setData({
                    ext: newData
                })
            }
        },
        isPreview: {
            type: Boolean,
            value: "",
            observer: function (newData, olddata) {
                this.setData({
                    isPreview: newData
                })
            }
        }
    },
    data: {
        domain: app.globalData.qiNiuDomain
    },
    ready: function () {
        let that = this
        if (that.data.src != "") {
            that.setData({
                urls: [that.data.src]
            })
        }
    },
    methods: {
        preview: function (e) {
            if (!this.data.isPreview)
                return true
            let that = this,
                index = e.currentTarget.dataset.index,
                urls = app.fullImg(that.data.urls)
            wx.previewImage({
                current: urls[index],
                urls: urls
            })
        },
    }
})