<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午6:42
 */

namespace frontend\controllers;

use common\models\UserQuestionType;
use frontend\models\UserIdentify;
use common\tools\Tool;
use common\tools\WxApp;
use Yii;

class UserController extends BaseController
{
    public function actionCheckTid($tid = 0) {
        return Tool::reJson(["result" => !!$this->getUser()->getTidExpire($tid)]);
    }

    // 获取用户信息
    public function actionUserInfo() {
        return Tool::reJson(["user" => $this->getUser()->info()]);
    }

    //小程序登录
    public function actionAppLogin() {
        $code = $this->getPost("code");
        $user = UserIdentify::findUserByAppCode($code);
        if ($user) {
            Yii::$app->user->login($user, 3600 * 2);
            $user = Yii::$app->user->identity;
            /* @var $user UserIdentify */
            return Tool::reJson(["user" => $user->info()]);
        } else
            return Tool::reJson(null, "登录失败", Tool::FAIL);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return Tool::reJson(1);
    }

    //小程序 录入用户的基本信息
    public function actionAppUser() {
        $user = $this->getUser();
        /* @var $user UserIdentify */
        $rawData = $this->getPost("rawData");
        $signature = $this->getPost("signature");
        if ($user->verifyUserInfo($rawData, $signature))
            return Tool::reJson(["user" => $user->info()]);
        return Tool::reJson(null, "用户信息不匹配失败", Tool::NEED_LOGIN);
    }


    public function actionPhoneDecrypt() {
        $user = $this->getUser();
        if (WxApp::decryptData($this->getPost("encryptedData"), $this->getPost("iv"), $user->session_key, $data) == WxApp::OK) {
            $data = json_decode($data, true);
            if (isset($data['phoneNumber']) && !empty($data['phoneNumber']) && isset($data['purePhoneNumber']) && !empty($data['purePhoneNumber'])) {
                return Tool::reJson(["purePhoneNumber" => $data['purePhoneNumber']]);
            } else
                return Tool::reJson(null, "您未绑定手机号", Tool::FAIL);
        } else
            return Tool::reJson(null, "解析手机号失败，请重新登录", Tool::FAIL);
    }
}