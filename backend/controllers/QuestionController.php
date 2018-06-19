<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:48
 */

namespace backend\controllers;


use common\models\QuestionPrice;
use common\models\QuestionType;

class QuestionController extends BaseController
{
    public $basicActions = ["type-info", "price-info"];

    public function actionTypes($tid = 0, $status = 0) {
        return $this->render("types", [
            "types" => QuestionType::getList($tid, $status)
        ]);
    }

    public function actionTypeInfo($id) {
        $type = QuestionType::findOne($id);

    }

    public function actionPrices($tid = 0, $status = 0) {
        return $this->render("prices", [
            "prices" => QuestionPrice::prices($tid, $status)
        ]);
    }

    public function actionPriceInfo($id = 0) {
        $price = QuestionPrice::findOne($id);
    }

}