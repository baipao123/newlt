<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/13
 * Time: 下午7:08
 */

namespace layuiAdm\actions;

use Yii;
use Qiniu\Auth;
use yii\base\Action;

class QiNiuTokenAction extends Action
{
    public $accessKey;
    public $secretKey;
    public $bucket;

    public function init() {
        parent::init();
        if (empty($this->accessKey))
            $this->accessKey = Yii::$app->params['qiniu']['ak'];
        if (empty($this->secretKey))
            $this->secretKey = Yii::$app->params['qiniu']['sk'];
        if (empty($this->bucket))
            $this->bucket = Yii::$app->params['qiniu']['bucket'];
    }

    public function run() {
        $token = $this->getToken();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        echo json_encode(["token" => $token]);
    }

    public function getToken($time = 7200, $refresh = false) {
        $cacheKey = "BaiPao123-QiNiu-Js-UpToken";
        if (!$refresh && Yii::$app->cache->exists($cacheKey))
            return Yii::$app->cache->get($cacheKey);
        $token = $this->generateToken($time);
        if ($time > 3600)
            Yii::$app->cache->set($cacheKey, $token, $time - 3600);
        return $token;
    }

    public function generateToken($time = 7200) {
        if ($time < 300)
            $time = 3600;
        $policy = [
            'saveKey'    => '$(etag)$(ext)',
            'returnBody' => '{"key": $(key),"size": $(fsize),"w": $(imageInfo.width),"h": $(imageInfo.height)}',
        ];
        $qiniu = new Auth($this->accessKey, $this->secretKey);
        $token = $qiniu->uploadToken($this->bucket, null, (int)$time, $policy);
        return $token;
    }

}