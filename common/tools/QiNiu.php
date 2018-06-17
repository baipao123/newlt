<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/24
 * Time: 下午6:41
 */

namespace common\tools;

use Qiniu\Auth;
use Yii;

class QiNiu
{

    public static function getToken($time = 7200, $refresh = false) {
        $cacheKey = "BaiPao123-QiNiu-Js-UpToken";
        if (!$refresh && Yii::$app->cache->exists($cacheKey))
            return Yii::$app->cache->get($cacheKey);
        $token = self::generateToken($time);
        if ($time > 3600)
            Yii::$app->cache->set($cacheKey, $token, $time - 3600);
        return $token;
    }

    public static function generateToken($time = 7200) {
        if ($time < 300)
            $time = 3600;
        $policy = [
            'saveKey'    => '$(etag)$(ext)',
            'returnBody' => '{"key": $(key),"size": $(fsize),"w": $(imageInfo.width),"h": $(imageInfo.height)}',
        ];
        $qiniu = new Auth(Yii::$app->params['qiniu']['ak'], Yii::$app->params['qiniu']['sk']);
        $token = $qiniu->uploadToken(Yii::$app->params['qiniu']['bucket'], null, (int)$time, $policy);
        return $token;
    }

}