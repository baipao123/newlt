<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/2
 * Time: 上午9:40
 */

namespace console\controllers;


use yii\console\Controller;

class ToolController extends Controller
{
    public function actionRefreshSchema(){
        \Yii::$app->db->schema->refresh();
    }

}