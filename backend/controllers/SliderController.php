<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/27
 * Time: 下午8:55
 */

namespace backend\controllers;

use Yii;
use common\models\Slider;
use yii\data\Pagination;

class SliderController extends BaseController
{
    public $basicActions = ["info", "toggle-status"];

    public function actionList($status = 0, $start_date = "", $end_date = "") {
        $query = Slider::find()->where(["<>", "status", Slider::STATUS_DEL]);
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        if (!empty($start_date) || !empty($end_date)) {
            $start_at = empty($start_date) ? 0 : strtotime($start_date);
            $end_at = empty($end_date) ? time() : strtotime($end_date) + 2483600 - 1;
            $query->andWhere(["OR", [">", "end_at", $start_at], ["end_at" => 0]])
                ->andWhere(["<=", "start_at", $end_at]);
        }
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $records = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->orderBy([
            "status"     => [Slider::STATUS_ON, Slider::STATUS_EXPIRE, Slider::STATUS_OFF],
            "created_at" => SORT_DESC
        ])->all();
        return $this->render("list", [
            "records"    => $records,
            "pagination" => $pagination,
        ]);
    }


    public function actionInfo($id = 0) {
        $slider = null;
        $title = $id > 0 ? "编辑轮播图" : "添加轮播图";
        if ($id > 0) {
            $slider = Slider::findOne($id);
            if (!$slider || $slider->status == Slider::STATUS_DEL) {
                Yii::$app->session->setFlash("danger", "未找到轮播");
                return $this->render("../layouts/none");
            }
        } else
            $slider = new Slider;
        if (Yii::$app->request->isPost) {
            $slider->title = $_POST['title'];
            $slider->cover = $_POST['cover'];
            $slider->type = $_POST['type'];
            $slider->tid = intval($_POST['tid']);
            $slider->link = $_POST['link'];
            $slider->sort = intval($_POST['sort']);
            $slider->status = $_POST['status'];
            $slider->start_at = empty($_POST['start_date']) ? time() : strtotime($_POST['start_date']);
            $end_at = strtotime($_POST['end_date']);
            $slider->end_at = empty($end_at) ? 0 : $end_at + 24 * 3600 - 1;
            $slider->aid = Yii::$app->user->id;
            if ($slider->isNewRecord)
                $slider->created_at = time();
            else
                $slider->updated_at = time();
            if (!$slider->save()) {
                Yii::$app->session->setFlash("info", $title . "失败");
                Yii::warning($slider->errors, "保存Slider出错");
            } else
                Yii::$app->session->setFlash("success", $title . "成功");
        }
        return $this->render("info", [
            "slider" => $slider
        ]);
    }

    public function actionToggleStatus($id, $status) {
        $slider = Slider::findOne($id);
        if (!$slider || $slider->status == Slider::STATUS_DEL) {
            Yii::$app->session->setFlash("danger", "未找到轮播");
            return $this->render("../layouts/none");
        }
        $arr = [
            Slider::STATUS_ON  => "上架",
            Slider::STATUS_OFF => "下架",
            Slider::STATUS_DEL => "删除"
        ];
        $title = $arr[ $status ] . "轮播";
        if ($status == Slider::STATUS_DEL && $slider->status != Slider::STATUS_OFF) {
            Yii::$app->session->setFlash("danger", "下架轮播才能够删除");
            return $this->render("../layouts/none");
        }
        $slider->status = $status;
        $slider->aid = Yii::$app->user->id;
        $slider->updated_at = time();
        if ($slider->save())
            Yii::$app->session->setFlash("success", $title . "成功");
        else
            Yii::$app->session->setFlash("danger", $title . "失败");
        return $this->render("../layouts/none");
    }
}