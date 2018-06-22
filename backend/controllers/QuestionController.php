<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:48
 */

namespace backend\controllers;


use Yii;
use common\models\QuestionPrice;
use common\models\QuestionType;
use common\tools\Status;
use layuiAdm\tools\Url;

class QuestionController extends BaseController
{
    public $basicActions = ["type-children", "type-info", "type-toggle", "price-info"];

    public function actionTypes($status = 0) {
        return $this->render("types", [
            "types" => QuestionType::getList(0, $status)
        ]);
    }

    public function actionTypeChildren($tid = 0, $status = 0) {
        if ($tid <= 0)
            return $this->redirect(Url::createLink("question/types", ["status" => $status]));
        return $this->render("type-children", [
            "types" => QuestionType::getList($tid, $status)
        ]);
    }

    public function actionTypeInfo($tid = 0) {
        $type = QuestionType::findOne($tid);

    }

    public function actionTypeToggle($tid, $status = 0) {
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status == Status::DELETE)
            Yii::$app->session->setFlash("danger", "分类不存在或已删除");
        else if (!in_array($status, [Status::PASS, Status::FORBID, Status::DELETE]))
            Yii::$app->session->setFlash("danger", "未知操作");
        else if ($status == $type->status)
            Yii::$app->session->setFlash("danger", "请勿重复操作");
        else if ($status == Status::DELETE && $type->status == Status::PASS)
            Yii::$app->session->setFlash("danger", "只有不通过的分类才能删除");
        else {
            $type->status = $status;
            $type->updated_at = time();
            $type->save();
            Yii::$app->session->setFlash("success", "操作成功");
        }
        return $this->alert();
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