<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/10
 * Time: 下午11:05
 */

namespace backend\controllers;

use layuiAdm\Init;
use layuiAdm\actions\QiNiuTokenAction;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class BaseController extends Controller {

    public $enableCsrfValidation = false;

    public $basicActions = [];

    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
            'qiniu-token' => [
                'class' => QiNiuTokenAction::className(),
            ],
        ]);
    }

    public function beforeAction($action) {
        Init::Layout($action);
        return parent::beforeAction($action);
    }


    public function alert(){
        return $this->render("/layouts/none");
    }
}