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
use yii\data\Pagination;

class QuestionController extends BaseController
{
    public $basicActions = ["type-children", "type-info", "type-toggle", "price-info", "price-toggle"];

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
            Yii::warning($_POST);
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
            elseif ($type->save()) {
                Yii::$app->session->setFlash("success", "保存成功");
                $type->updateSetting(Yii::$app->request->post("setting"));
            } else {
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
            if ($status == Status::DELETE)
                QuestionPrice::updateAll(["status" => Status::DELETE], ["tid" => $tid]);
        }
        return $this->alert();
    }

    public function actionPrices($tid = 0, $status = 0) {
        $query = QuestionPrice::find();
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        if ($tid > 0)
            $query->andWhere(["tid" => $tid]);

        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $prices = $query->orderBy([
            "status" => [Status::PASS, Status::EXPIRE, Status::FORBID],
            "tid"    => SORT_ASC,
            "sort"   => SORT_DESC
        ])->offset($pagination->getOffset())->limit($pagination->getLimit())->all();


        return $this->render("prices", [
            "prices"     => $prices,
            "types"      => QuestionType::getList(0, 0),
            "pagination" => $pagination,
            "tid"        => $tid,
            "status"     => $status
        ]);
    }

    public function actionPriceInfo($id = 0) {
        if (empty($id))
            $price = new QuestionPrice();
        else {
            $price = QuestionPrice::findOne($id);
            if (!$price || $price->status == Status::DELETE) {
                Yii::$app->session->setFlash("danger", "未找到价格或价格已删除");
                return $this->alert();
            }
        }

        if (Yii::$app->request->isPost) {
            $price->tid = (int)Yii::$app->request->post("tid");
            $price->title = '';
//            $price->title = Yii::$app->request->post("title");
            $price->cover = Yii::$app->request->post("cover","");
            $price->price = (int)strval(strval(Yii::$app->request->post("price")) * 100);
            $price->oldPrice = (int)strval(strval(Yii::$app->request->post("oldPrice")) * 100);
            $price->type = Yii::$app->request->post("type");
            $hour = (int)Yii::$app->request->post("hour");
            $price->hour = $price->type == QuestionPrice::Type_Day ? $hour * 24 : $hour;
            $price->status = (int)Yii::$app->request->post("status");
            $price->note = Yii::$app->request->post("note","");
            $price->sort = (int)Yii::$app->request->post("sort");

            $time = Yii::$app->request->post("datetime");
            if (empty($time)) {
                $price->start_at = 0;
                $price->end_at = 0;
            } else {
                $timeArr = explode(" - ", $time);
                $price->start_at = !empty($timeArr[0]) ? strtotime($timeArr[0]) : 0;
                $price->end_at = isset($timeArr[1]) && !empty($timeArr[1]) ? strtotime($timeArr[1]) : 0;
            }

            if ($price->isNewRecord)
                $price->created_at = time();
            $price->updated_at = time();

            if (empty($price->tid))
                Yii::$app->session->setFlash("warning", "请选择分类");
//            elseif (empty($price->title))
//                Yii::$app->session->setFlash("warning", "请输入标题");
            elseif (empty($price->price))
                Yii::$app->session->setFlash("warning", "请输入价格");
            elseif (!in_array($price->type, [QuestionPrice::Type_Day, QuestionPrice::Type_Hour]))
                Yii::$app->session->setFlash("warning", "请选择正确的时长类型");
            elseif (empty($price->hour))
                Yii::$app->session->setFlash("warning", "请输入时长");
            elseif ($price->start_at > $price->end_at && $price->end_at > 0)
                Yii::$app->session->setFlash("warning", "请选择正确的上架时间");
            elseif ($price->save())
                Yii::$app->session->setFlash("success", "保存成功");
            else {
                Yii::$app->session->setFlash("warning", "保存失败");
                Yii::warning($price->errors, "保存QuestionPrice失败");
            }

        }
        return $this->render("price-info", [
            "price" => $price,
            "types"      => QuestionType::getList(0, 0),
        ]);
    }

    public function actionPriceToggle($tid, $status = 0) {
        $type = QuestionPrice::findOne($tid);
        if (!$type || $type->status == Status::DELETE)
            Yii::$app->session->setFlash("danger", "价格不存在或已删除");
        else if (!in_array($status, [Status::PASS, Status::FORBID, Status::DELETE]))
            Yii::$app->session->setFlash("danger", "未知操作");
        else if ($status == $type->status)
            Yii::$app->session->setFlash("danger", "请勿重复操作");
        else if ($status == Status::DELETE && $type->status == Status::PASS)
            Yii::$app->session->setFlash("danger", "只有下架的价格才能删除");
        else {
            $type->status = $status;
            $type->updated_at = time();
            $type->save();
            Yii::$app->session->setFlash("success", "操作成功");
        }
        return $this->alert();
    }

}