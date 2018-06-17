<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/23
 * Time: 下午8:16
 */

namespace common\tools;

use common\models\SmsRecord;
use Yii;

class Sms
{
    public static function send($uid, $phone, $code = "") {
        if (!StringHelper::isMobile($phone))
            return "手机号不正确";
        if (self::isTooMorePhone($phone) || self::isTooMoreUser($uid))
            return "15分钟内每个手机号、每个用户只能发送5条验证码";
        if (empty($code))
            $code = self::getCode($uid, $phone);
        $record = new SmsRecord;
        $record->phone = $phone;
        $record->code = $code;
        $record->status = SmsRecord::IsSend;
        $record->created_at = time();
        $record->save();
        AliSms::send($phone, ["code" => $code]);
        return true;
    }

    public static function isTooMoreUser($uid) {
        $key = "SMS-UID-NUM:" . $uid;
        $num = Yii::$app->redis->incr($key);
        Yii::$app->redis->expire($key, 900);
        return $num > 5;
    }

    public static function isTooMorePhone($phone) {
        $key = "SMS-PHONE-NUM:" . $phone;
        $num = Yii::$app->redis->incr($key);
        Yii::$app->redis->expire($key, 900);
        return $num > 5;
    }

    public static function getCode($uid, $phone) {
        $cacheKey = "SMS-CODE-UID-" . $uid . "-phone-" . $phone;
        if (Yii::$app->cache->exists($cacheKey))
            return Yii::$app->cache->get($cacheKey);
        $nonce = sprintf("%06d", mt_rand(0, 999999));
        Yii::$app->cache->set($cacheKey, $nonce, 600);
        return $nonce;
    }

    public static function VerifyCode($phone, $code) {
        $record = SmsRecord::find()->where(["phone" => $phone])->orderBy("created_at DESC")->one();
        /* @var $record SmsRecord */
        if (!$record || $record->code != $code || $record->created_at + 600 < time())
            return false;
        $cacheKey = "SMS-VERIFY-PASS-UID:" . Yii::$app->user->id;
        Yii::$app->cache->set($cacheKey, "PASS", 180);
        return true;
    }

    public static function hasVerified($uid) {
        $cacheKey = "SMS-VERIFY-PASS-UID:" . $uid;
        if (Yii::$app->cache->exists($cacheKey) && Yii::$app->cache->get($cacheKey) == "PASS")
            return true;
        return false;
    }
}