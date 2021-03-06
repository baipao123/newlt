<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 15:23:34
 */

namespace console\controllers;

use common\models\Question;
use common\models\QuestionType;
use common\models\UserExam;
use Yii;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionIndex() {
        $total = 0;
        for ($i = 6; $i <= 20; $i++) {
            $type = QuestionType::findOne($i);
            for ($j = 1; $j <= 3; $j++) {
                $num = intval( Question::find()->where(["tid" => $i, "type" => $j])->count());
                $type->updateSetting([$type->typeEnStr($j) . "Total" => $num]);
                $total += $num;
            }
        }
        echo $total;
    }

}