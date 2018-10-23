<?php

namespace common\models;

use common\tools\Img;
use common\tools\Status;
use common\tools\Tool;
use common\tools\WxApp;
use console\worker\SendTpl;
use Yii;

/**
 * @property QuestionType $questionType
 */
class User extends \common\models\base\User
{

    public function getQuestionType() {
        return $this->hasOne(QuestionType::className(), ["id" => "tid2"]);
    }

    public function getTidExpire($tid) {
        if ($tid <= 0)
            return $this->expire_at;
        if ($this->tid == $tid || $this->tid2 == $tid)
            return $this->expire_at > time() ? $this->expire_at : 0;
        $questionType = QuestionType::findOne($tid);
        if (!$questionType)
            return 0;
        $tid = $questionType->tid > 0 ? $questionType->tid : $tid;
        $type = UserQuestionType::find()->where(["uid" => $this->id, "tid" => $tid])->andWhere([">", "expire_at", time()])->orderBy("expire_at desc")->one();
        /* @var $type UserQuestionType */
        return $type ? $type->expire_at : 0;
    }

    public function updateTrainRecord($tid, $type, $offset) {
        if ($tid <= 0 || $offset <= 1)
            return false;
        return Yii::$app->db->createCommand("INSERT INTO `user_train_record` (`uid`, `tid`, `type`, `offset`, `last_at`) VALUE (:uid, :tid, :type, :offset, :time) ON DUPLICATE KEY UPDATE `offset`=:offset,`last_at`=:time;", [":uid" => $this->id, ":tid" => $tid, ":type" => $type, ":offset" => $offset, ":time" => time()])->execute();
    }


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
            "uid"       => $this->id,
            "tid"       => $this->tid,
            "tName"     => $this->questionType ? $this->questionType->name : "",
            "expire_at" => $this->expire_at <= time() ? 0 : $this->expire_at,
            "nickname"  => $this->nickname,
            "realname"  => $this->realname,
            "avatar"    => Img::format($this->avatar),
            "phone"     => $this->phone(),
            "purePhone" => $this->phone(true),
            "gender"    => $this->gender
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
