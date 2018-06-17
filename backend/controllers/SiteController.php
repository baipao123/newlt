<?php

namespace backend\controllers;

use common\models\Admin;
use common\models\Company;
use common\models\CompanyRecord;
use common\models\Job;
use common\models\User;
use layuiAdm\Init;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return ArrayHelper::merge(parent::actions(), Init::SiteActions("需求列表"), [

        ]);
    }

    public function actionHome() {

        $panels = [
            [
                "color" => "red",
                "title" => Admin::find()->where(["status" => Admin::STATUS_ACTIVE])->count(),
                "icon"  => "icon-clock",
                "desc"  => "账户列表",
                "href"  => "/admin/list",
            ]
        ];

        return $this->render("index", [
            "panels" => $panels,
        ]);
    }

    /**
     * Login action.
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $assetUrl = Yii::$app->assetManager->publish('@layuiAdm/assets')[1];
        Yii::$app->view->registerCssFile($assetUrl . "/layui/css/layui.css", ["position" => View::POS_HEAD]);
        Yii::$app->view->registerJsFile($assetUrl . "/layui/layui.all.js", ["position" => View::POS_HEAD]);

        $error = "";
        $username = "";
        if (Yii::$app->request->isPost) {
            $username = Yii::$app->request->post("username", "");
            $identify = \backend\models\UserIdentify::findByUsername($username);
            if (!$identify)
                $error = "用户名不存在";
            else if ($identify->checkPassword(Yii::$app->request->post("password", ""))) {
                if (Yii::$app->user->login($identify, Yii::$app->request->post("remember", 0) > 0 ? 3600 * 24 * 30 : 3600))
                    return $this->goBack();
                else
                    $error = "登陆失败";
            } else
                $error = "密码错误";
        }
        return $this->renderPartial('login', [
            "username" => $username,
            "error"    => $error,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect("/site/login");
    }
}
