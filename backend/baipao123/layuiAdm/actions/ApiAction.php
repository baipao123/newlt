<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午6:03
 */

namespace layuiAdm\actions;

use Yii;
use yii\base\Action;

class ApiAction extends Action
{
    public function init() {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }

    public function run(){

    }
}