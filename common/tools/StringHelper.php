<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2017/8/31
 * Time: 下午10:15
 */

namespace common\tools;

class StringHelper extends \yii\helpers\StringHelper{
    /**
     * @param int $length 随机字符串长度
     * @param int $unify 统一大小写：0：混排，1：统一小写，2：统一大写
     * @param bool $ignore 忽略容易混淆的字母、数字 0、1、l、L、o、O
     * @return string
     */
    public static function nonce($length = 8, $unify = 0, $ignore = false) {
        if (!in_array($unify, [0, 1, 2])) {
            $unify = 0;
        }
        
        if ($ignore) {
            if ($unify == 0) {
                $string = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";
            }
            else {
                $string = "23456789abcdefghijkmnpqrstuvwxyz";
            }
        }
        else {
            if ($unify == 0) {
                $string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            }
            else {
                $string = "0123456789abcdefghijklmnopqrstuvwxyz";
            }
        }
        
        $res = "";
        $len = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $res .= substr($string, mt_rand(0, $len - 1), 1);
        }
        
        if ($unify == 2) {
            return strtoupper($res);
        }
        elseif ($unify == 1) {
            return strtolower($res);
        }
        else {
            return $res;
        }
    }

    public static function isRealName($str) {
        return (bool)preg_match('/^[\x00-\xff]{2,}$/', $str);
    }

    public static function isMobile($mobile) {
        return (bool)preg_match('/1[1-9]([0-9]){9}/', $mobile);
    }

    // 支付需要过滤一下商品标题    长度、特殊字符要求
    public static function formatTitleForPay($title, $length = 40, $maxLength = 128) {
        //        $title = trim($title, "-  　");
        $char = ["=", "&"];
        // 过滤特殊字符
        // 支持的：# @ \ ^ & _ - + 。. ...
        // 不支持的： 支付宝：= &
        $title = preg_replace('/(' . implode("|", $char) . ')/', '', $title);
        if (strlen($title) > $maxLength) {
            $title = self::truncate($title, $length);
        }
        return $title;
    }
}