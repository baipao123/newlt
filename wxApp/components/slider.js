const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        sliders: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
                this.setData({
                    sliders: newData
                })
            }
        }
    },
    data: {
        windowWidth: 300,
        domain: app.globalData.qiNiuDomain
    },
    ready: function () {
        let that = this
        app.getSystemInfo(function (info) {
            that.setData({
                windowWidth: info.windowWidth
            })
        })
    },
    methods: {
        sliderTap: function (e) {
            let that = this,
                index = e.currentTarget.dataset.index,
                slider = that.properties.sliders[index]
            switch (slider.type) {
                case 0:
                    break;
                case 1:
                    app.turnPage("job/info?id=" + slider.tid)
                    break;
                case 2:
                    app.turnPage(slider.link)
                    break;
                case 3:
                    app.turnPage("common/webView?url=" + slider.link)
                    break;
                default:
                    break;
            }
        }
    }
})