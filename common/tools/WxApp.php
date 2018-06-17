<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 15:07:15
 */

namespace common\tools;

use Yii;

class WxApp extends Wx
{
    const OK = 0;
    const IllegalAesKey = -41001;
    const IllegalIv = -41002;
    const IllegalBuffer = -41003;
    const DecodeBase64Error = -41004;


    protected static function getAppId() {
        return Yii::$app->params['wxApp']['appId'];
    }

    protected static function getAppSecret() {
        return Yii::$app->params['wxApp']['appSecret'];
    }

    public static function decryptUserCode($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session";
        $params = [
            "appid"      => self::getAppId(),
            "secret"     => self::getAppSecret(),
            "js_code"    => $code,
            "grant_type" => "authorization_code"
        ];
        
        $response = self::http($url,$params);
        return json_decode($response,true);
    }

    public static function decryptData($encryptedData, $iv, $sessionKey, &$data) {
        if (strlen($sessionKey) != 24)
            return self::IllegalAesKey;
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24)
            return self::IllegalIv;
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL)
            return self::IllegalBuffer;
        if ($dataObj->watermark->appid != self::getAppId())
            return self::IllegalBuffer;
        $data = $result;
        return self::OK;
    }

    //获取access_token
    public static function getAccessToken($appId = "", $appSecret = "", $refresh = false) {
        $appId = empty($appId) ? self::getAppId() : $appId;
        $appSecret = empty($appSecret) ? self::getAppSecret() : $appSecret;
        return parent::getAccessToken($appId, $appSecret, $refresh);
    }

    //发送小程序模板消息
    public static function sendTpl($accessToken, $openId, $tplId, $data, $page, $formId, $keyword) {
        if (empty($accessToken))
            return "获取access_token失败";
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $accessToken;
        $tplData = [
            "touser"           => $openId,
            "template_id"      => $tplId,
            "page"             => $page,
            "form_id"          => $formId,
            "data"             => $data,
            "emphasis_keyword" => $keyword
        ];
        $res = self::http($url, [], $tplData);
        $response = json_decode($res, true);
        if ($res && $response && isset($response['errcode']) && $response['errcode'] == 0 && isset($response['errmsg']) && $response['errmsg'] == "ok")
            return true;
        elseif ($res && $response && isset($response['msg']))
            return $response['msg'];
        else
            return false;
    }
}