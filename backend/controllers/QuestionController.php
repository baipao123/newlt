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

    public function actionTypes($tid = 0, $status = 0) {
        if ($tid > 0)
            Yii::$app->controller->layout = "@layuiAdm/views/layouts/basic.php";
        else
            $tid = 0;
        return $this->render("types", [
            "tid"   => $tid,
            "types" => QuestionType::getList($tid, $status)
        ]);
    }

    public function actionTypeInfo($tid = 0, $pid = 0) {
        if (empty($tid)) {
            $type = new QuestionType();
            $type->parentId = $pid;
        } else {
            $type = QuestionType::findOne($tid);
            if (!$type || $type->status == Status::DELETE) {
                Yii::$app->session->setFlash("danger", "未找到分类或分类已删除");
                return $this->alert();
            }
        }
        if (Yii::$app->request->isPost) {
            $type->name = Yii::$app->request->post("name");
            $type->icon = Yii::$app->request->post("icon");
            $type->sort = Yii::$app->request->post("sort");
            $type->status = Yii::$app->request->post("status") ? Status::PASS : Status::FORBID;
            $type->updated_at = time();
            if ($type->isNewRecord)
                $type->created_at = time();
            if (empty($type->name))
                Yii::$app->session->setFlash("warning", "名称必填");
            elseif (empty($type->icon) && $type->parentId == 0)
                Yii::$app->session->setFlash("warning", "请上传图标");
            elseif ($type->save())
                Yii::$app->session->setFlash("success", "保存成功");
            else {
                Yii::$app->session->setFlash("warning", "保存失败");
                Yii::warning($type->errors, "保存QuestionType失败");
            }
        }
        return $this->render("type-info", [
            "type" => $type
        ]);
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