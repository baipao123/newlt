// created by gpake
(function () {
    const app = getApp()

    let config = {
        qiniuRegion: 'ECN',
        qiniuImageURLPrefix: app.globalData.qiNiuDomain,
        qiniuUploadToken: '',
        qiniuUploadTokenURL: app.globalData.apiDomain + 'base/qiniu-token',
        qiniuUploadTokenFunction: null,
        qiniuShouldUseQiniuFileName: true
    }

    module.exports = {
        upload: upload,
        choseImg: choseImg,
    }

    function updateConfigWithOptions(options) {
        if (options.region)
            config.qiniuRegion = options.region;
        if (options.uptoken)
            config.qiniuUploadToken = options.uptoken;
        if (options.uptokenURL)
            config.qiniuUploadTokenURL = options.uptokenURL;
        if (options.uptokenFunc)
            config.qiniuUploadTokenFunction = options.uptokenFunc;
        if (options.domain)
            config.qiniuImageURLPrefix = options.domain;
        if (options.hasOwnProperty("shouldUseQiniuFileName"))
            config.qiniuShouldUseQiniuFileName = options.shouldUseQiniuFileName
    }

    function upload(filePath, success, fail, options, progress) {
        if (null == filePath) {
            console.error('qiniu uploader need filePath to upload');
            return;
        }
        if (options) {
            updateConfigWithOptions(options);
        }
        if (config.qiniuUploadToken) {
            doUpload(filePath, success, fail, options, progress);
        } else if (config.qiniuUploadTokenURL) {
            getQiniuToken(function () {
                doUpload(filePath, success, fail, options, progress);
            });
        } else if (config.qiniuUploadTokenFunction) {
            config.qiniuUploadToken = config.qiniuUploadTokenFunction();
            if (null == config.qiniuUploadToken && config.qiniuUploadToken.length > 0) {
                console.error('qiniu UploadTokenFunction result is null, please check the return value');
                return
            }
            doUpload(filePath, success, fail, options, progress);
        } else {
            console.error('qiniu uploader need one of [uptoken, uptokenURL, uptokenFunc]');
            return;
        }
    }

    function doUpload(filePath, success, fail, options, progress) {
        if (null == config.qiniuUploadToken && config.qiniuUploadToken.length > 0) {
            console.error('qiniu UploadToken is null, please check the init config or networking');
            return
        }
        let url = uploadURLFromRegionCode(config.qiniuRegion);
        let fileName = filePath.split('//')[1];
        if (options && options.key) {
            fileName = options.key;
        }
        let formData = {
            'token': config.qiniuUploadToken
        };
        if (!config.qiniuShouldUseQiniuFileName) {
            formData['key'] = fileName
        }
        let uploadTask = wx.uploadFile({
            url: url,
            filePath: filePath,
            name: 'file',
            formData: formData,
            success: function (res) {
                let dataString = res.data
                if (res.data.hasOwnProperty('type') && res.data.type === 'Buffer') {
                    dataString = String.fromCharCode.apply(null, res.data.data)
                }
                try {
                    let dataObject = JSON.parse(dataString);
                    //do something
                    let imageUrl = config.qiniuImageURLPrefix + dataObject.key;
                    dataObject.imageURL = imageUrl;
                    console.log(dataObject);
                    if (success) {
                        success(dataObject);
                    }
                } catch (e) {
                    console.log('parse JSON failed, origin String is: ' + dataString)
                    if (fail) {
                        fail(e);
                    }
                }
            },
            fail: function (error) {
                console.error(error);
                if (fail) {
                    fail(error);
                }
            }
        })

        uploadTask.onProgressUpdate((res) => {
            progress && progress(res)
        })
    }

    function getQiniuToken(callback) {
        wx.request({
            url: config.qiniuUploadTokenURL,
            success: function (res) {
                let token = res.data.uptoken;
                if (token && token.length > 0) {
                    config.qiniuUploadToken = token;
                    if (callback) {
                        callback();
                    }
                } else {
                    console.error('qiniuUploader cannot get your token, please check the uptokenURL or server')
                }
            },
            fail: function (error) {
                console.error('qiniu UploadToken is null, please check the init config or networking: ' + error);
            }
        })
    }

    function uploadURLFromRegionCode(code) {
        let uploadURL = null;
        switch (code) {
            case 'ECN':
                uploadURL = 'https://up.qbox.me';
                break;
            case 'NCN':
                uploadURL = 'https://up-z1.qbox.me';
                break;
            case 'SCN':
                uploadURL = 'https://up-z2.qbox.me';
                break;
            case 'NA':
                uploadURL = 'https://up-na0.qbox.me';
                break;
            case 'ASG':
                uploadURL = 'https://up-as0.qbox.me';
                break;
            default:
                console.error('please make the region is with one of [ECN, SCN, NCN, NA, ASG]');
        }
        return uploadURL;
    }

    function choseImg(count, success) {
        // 选择图片
        wx.chooseImage({
            count: count,
            success: function (res) {
                let index = 0;
                for (let path of res.tempFilePaths) {
                    index++
                    upload(path, (info) => {
                        success(info, index)
                    }, (error) => {
                        console.log('error: ' + error)
                    });
                }
            }
        })
    }

})();
