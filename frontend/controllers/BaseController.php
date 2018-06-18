<?php

namespace frontend\controllers;

use common\tools\QiNiu;
use common\tools\Tool;
use frontend\models\UserIdentify;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;


class BaseController extends Controller
{
    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
            "error" => 'frontend\actions\base\ErrorAction'
        ]);
    }

    public function beforeAction($action) {
        if (Yii::$app->user->isGuest && !in_array($action->id, ["app-login", "error", "qiniu-token"])) {
            echo json_encode(Tool::reJson(null, "请先登录", Tool::NEED_LOGIN));
            return false;
        }
        Yii::$app->response->format = 'json';
        return parent::beforeAction($action);
    }

    public function getPost($name = "", $defaultValue = "") {
        return Yii::$app->request->post($name, $defaultValue);
    }

    /**
     * @return UserIdentify
     */
    public function getUser() {
        return Yii::$app->user->getIdentity() ? Yii::$app->user->getIdentity() : new UserIdentify();
    }

    public function user_id(){
        return Yii::$app->user->id;
    }

    public function actionQiniuToken() {
        return ["uptoken" => QiNiu::getToken()];
    }
}
