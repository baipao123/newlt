const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        range: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        childKey: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        rangeKey: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        valueKey: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        value: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        show: {
            type: Boolean,
            value: false,
            observer: function (newData, oldData) {
                if(newData)
                    this.loadValue()
            }
        }
    },
    data: {
        ranges: [],
        title: '',
    },
    ready: function () {
    },
    methods: {
        loadValue:function () {
            let that = this,
                range = that.data.range,
                rangeKey = that.data.rangeKey,
                value = that.data.value,
                childKey = that.data.childKey,
                ranges = [],
                title = ''
            if(range.length == 0)
                return false
            ranges[0] = range
            title = range[value[0]][rangeKey[0]]
            for (let i = 1; i < value.length; i++) {
                ranges[i] = range[value[i - 1]][childKey[i - 1]]
                title += ' ' + ranges[i][value[i]][rangeKey[i]]
            }
            that.setData({
                ranges: ranges,
                value: value,
                title: title
            })
        },
        change: function (e) {
            let that = this,
                newValue = e.detail.value,
                value = that.data.value,
                ranges = that.data.ranges,
                childKey = that.data.childKey,
                i = 0,
                j
            console.log(newValue)
            for (i; i < value.length; i++) {
                if (i + 1 != value.length && newValue[i] != value[i]) {
                    for (j = i+1; j < value.length; j++) {
                        newValue[j] = 0
                    }
                    that.data.value = newValue
                    that.loadValue()
                    return false
                }
            }
            that.data.value = newValue
            that.loadValue()
        },
        empty: function () {

        },
        cancel: function () {
            this.setData({
                show: false
            })
        },
        submit: function (e) {
            let that = this,
                value = that.data.value,
                valueKey = that.data.valueKey,
                i = 0,
                result = [],
                ranges = that.data.ranges
            for (i; i < value.length; i++) {
                result.push(ranges[i][value[i]][valueKey[i]])
            }
            that.setData({
                show:false
            })
            this.triggerEvent('submit', {value: result})
        }
    }
})