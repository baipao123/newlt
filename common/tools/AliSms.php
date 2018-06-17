<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-17
 * Time: 16:34:47
 */

namespace common\tools;

use Yii;
use yii\helpers\ArrayHelper;

class AliSms
{
    const API_URL_DEFAULT = "dysmsapi.aliyuncs.com";

    protected static function getAccessKeyId() {
        return isset(Yii::$app->params['aliSms']['keyId']) ? Yii::$app->params['aliSms']['keyId'] : "";
    }

    protected static function getAccessKeySecret() {
        return isset(Yii::$app->params['aliSms']['keySecret']) ? Yii::$app->params['aliSms']['keySecret'] : "";
    }

    public static function send($phone, $templateParam = [], $templateCode = "", $signName = "", $OutId = "", $upExtendCode = "") {
        $params = [
            "PhoneNumbers"    => $phone,
            "SignName"        => $signName,
            "TemplateCode"    => $templateCode,
            "OutId"           => $OutId,
            "SmsUpExtendCode" => $upExtendCode,
            "TemplateParam"   => !empty($templateParam) && is_array($templateParam) ? json_encode($templateParam, JSON_UNESCAPED_UNICODE) : "",
            "RegionId"        => "cn-hangzhou",
            "Action"          => "SendSms",
            "Version"         => "2017-05-25",
        ];
        $res = self::request(self::API_URL_DEFAULT, $params, false);
        return json_decode($res, true);
    }

    public static function request($domain, $params, $isHtttps = false, $keyId = "", $keySecret = "") {
        $keyId = empty($keyId) ? self::getAccessKeyId() : $keyId;
        $keySecret = empty($keySecret) ? self::getAccessKeySecret() : $keySecret;
        if(empty($keyId) || empty($keySecret))
            return false;
        $sysParams = [
            "SignatureMethod"  => "HMAC-SHA1",
            "SignatureNonce"   => StringHelper::nonce(8),
            "SignatureVersion" => "1.0",
            "AccessKeyId"      => $keyId,
            "Timestamp"        => gmdate("Y-m-d\TH:i:s\Z"),
            "Format"           => "JSON",
        ];
        $params = ArrayHelper::merge($sysParams, $params);
        $signStr = self::getSignStr($params);
        $sign = self::sign($signStr, $keySecret);
        $url = ($isHtttps ? 'https' : 'http') . "://{$domain}/?Signature={$sign}{$signStr}";
        return self::fetchContent($url);
    }

    protected static function getSignStr($apiParams) {
        ksort($apiParams);
        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . self::encode($key) . "=" . self::encode($value);
        }
        return "GET&%2F&" . self::encode(substr($sortedQueryStringTmp, 1));
    }

    protected static function sign($stringToSign, $accessKeySecret) {
        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));
        $signature = self::encode($sign);
        return $signature;
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url) {
        Yii::info($url, "aliSms");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-sdk-client" => "php/2.0.0"
        ]);
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $rtn = curl_exec($ch);
        if ($rtn === false) {
            Yii::info("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), "aliSms");
        }
        Yii::info($rtn, "aliSms");
        curl_close($ch);
        return $rtn;
    }

}