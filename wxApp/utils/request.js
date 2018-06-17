/**
 * Created by huangchen on 2018/4/17.
 */
const app = getApp()

const domain = app.globalData.apiDomain

const post = (url, params, success, fail, complete) => {
    request("POST", url, params, success, fail, complete);
}

const get = (url, params, success, fail, complete) => {
    request("GET", url, params, success, fail, complete);
}

const request = (method, url, params, success, fail, complete, loginFail = false) => {
    if (url.substr(0, 5) != "https")
        url = domain + url;
    wx.request({
        url: url,
        data: params,
        method: method,
        dataType: "json",
        header: {
            "Content-Type": "application/x-www-form-urlencoded",
            cookie: wx.getStorageSync('cookie')
        },
        success: function (res) {
            if (res.data != undefined && res.data.code != undefined) {
                if (res.header['Set-Cookie'])
                    wx.setStorageSync('cookie', res.header['Set-Cookie']);
                if (res.data.hasOwnProperty("msg") && res.data.msg != '')
                    app.toast(res.data.msg, res.data.code == 0 ? "success" : "none")
                if (res.data.code == 0) {
                    if (typeof success == 'function')
                        success(res.data.data, res.data);
                } else if (res.data.code == -1) {
                    if (!loginFail) {
                        app.login(() => {
                            request(method, url, params, success, fail, complete, true)
                        });
                    }
                } else if (typeof fail == 'function')
                    fail(res.data)
            } else
                app.toast("500,服务器解析异常")
        },
        fail: fail,
        compete: complete
    });
}

module.exports = {
    post: post,
    get: get,
}