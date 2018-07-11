<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 15:23:34
 */

namespace console\controllers;

use common\models\UserExam;
use Yii;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionIndex(){
        echo var_export(UserExam::examInfo(1,20),true);
    }

}