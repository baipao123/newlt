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
        rangeKey:{
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        valueKey:{
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        },
        value:{
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        }
    },
    data: {
        ranges:[],
    },
    ready: function () {
        let that = this
        for(let i=0;i<that.data.value.length;i++){

        }
    },
    methods: {
        change:function(e){
            let that = this,
                newValue = e.detail.value,
                value = that.data.value,
                ranges = that.data.ranges,
                rangeKey = that.data.rangeKey,
                i=0,
                j
            for(i;i<value.length;i++){
                if(i+1 != value.length && newValue[i] != value[i]){
                    ranges[i+1] = ranges[i][rangeKey[i]]
                    for(j=i;j<value.length;j++){
                        ranges[j+2]=ranges[j+1][rangeKey[j+1]]
                    }
                    that.setData({
                        ranges:ranges,
                        value:newValue
                    })    
                    return false
                }
            }
            that.setData({
                value: newValue,
            })
        },
        empty:function(){

        },
        cancel:function(){
            this.setData({
                show:false
            })
        },
        submit:function(e){
            let that = this,
                value = that.data.value,
                valueKey = that.data.valueKey,
                i = 0,
                result=[],
                ranges = that.data.ranges
            for(i;i<value.length;i++){
                result.push(ranges[i][valueKey[i]])
            }
            this.triggerEvent('submit', {value:result})
        }
    }
})