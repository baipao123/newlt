<?php

namespace common\models;

use common\tools\Img;
use common\tools\Tool;
use common\tools\WxApp;
use console\worker\SendTpl;
use Yii;

class User extends \common\models\base\User
{
    const TYPE_USER = 1;
    const TYPE_COMPANY = 2;
    const TYPE_USER_BOSS = 3;

    /**
     * @param string $code
     * @return static
     */
    public static function findUserByAppCode($code) {
        $data = WxApp::decryptUserCode($code);
        if (!$data || !isset($data['openid']) || empty($data['openid']))
            return null;
        $user = static::findOne(["openId" => $data['openid']]);
        $user or $user = new static;
        $user->openId = $data['openid'];
        $user->unionId = isset($data['unionid']) ? $data['unionid'] : "";
        $user->session_key = isset($data['session_key']) ? $data['session_key'] : "";
        if ($user->isNewRecord)
            $user->created_at = time();
        $user->last_login = time();
        $user->save();
        return $user;
    }

    public function verifyUserInfo($rawData, $signature) {
        $sign = sha1($rawData . $this->session_key);
        if ($sign === $signature) {
            $userInfo = json_decode($rawData, true);
            $this->avatar = $userInfo['avatarUrl'];
            $this->nickname = $userInfo['nickName'];
            $this->gender = $userInfo['gender'];
            $this->cityName = $userInfo['city'];
            $this->province = $userInfo['province'];
            $this->country = $userInfo['country'];
            $this->save();
            return true;
        }
        return false;
    }

    public function info() {
        return [
            "uid"         => $this->id,
            "type"        => $this->type,
            "nickname"    => $this->nickname,
            "realname"    => $this->realname,
            "avatar"      => Img::format($this->avatar),
            "phone"       => $this->phone(),
            "purePhone"   => $this->phone(true),
            "gender"      => $this->gender
        ];
    }

    public function phone($full = false) {
        if (empty($this->phone) || $full)
            return $this->phone;
        return substr_replace($this->phone, '****', 3, 4);
    }


    public function sendTpl($type, $data, $formId, $page = "", $color = [], $keyword = "") {
        $tplData = [];
        for ($j = 1; $j <= count($data); $j++) {
            $tplData[ "keyword" . $j ] = [
                "value" => $data[ $j - 1 ]
            ];
            if (isset($color[ $j ]))
                $tplData[ "keyword" . $j ]['color'] = $color[ $j ];
        }
        $accessToken = WxApp::getAccessToken();
        WxApp::sendTpl($accessToken, $this->openId, $type, $tplData, $page, $formId, $keyword);
    }

    public function sendTplByQueue($type, $data, $formId, $page = "", $color = [], $keyword = "") {
        Yii::$app->queue->push(new SendTpl([
            "openId"  => $this->openId,
            "type"    => $type,
            "data"    => $data,
            "formId"  => $formId,
            "page"    => $page,
            "color"   => $color,
            "keyword" => $keyword
        ]));
    }
}
