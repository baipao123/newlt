<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:55:08
 */

namespace frontend\controllers;

use Yii;
use common\models\Order;
use common\models\QuestionPrice;
use common\models\QuestionType;
use common\tools\Status;
use common\tools\Tool;

class GoodsController extends BaseController
{
    public function actionTypes() {
        $types = QuestionType::getList();
        $data = [];
        foreach ($types as $type)
            $data[] = $type->info();
        return Tool::reJson([
            "types" => $data
        ]);
    }


    public function actionPrices($tid) {
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "分类不存在", Tool::FAIL);
        $prices = QuestionPrice::prices($tid);
        $data = [];
        foreach ($prices as $price)
            $data[] = $price->info();
        return Tool::reJson([
            "prices" => $data,
        ]);
    }

    public function actionOrder() {
        $pid = $this->getPost("pid", 0);
        $p = QuestionPrice::findOne($pid);
        if (!$p || $p->status != Status::PASS)
            return Tool::reJson(null, "未发现商品，或商品已下架", Tool::FAIL);
        $type = $p->questionType;
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "未发现商品，或商品已下架", Tool::FAIL);
        $oid = Order::find()->where(["tid" => $p->tid, "pid" => $pid, "status" => [Status::WAIT_PAY, Status::IS_UNIFY_ORDER, Status::WAIT_NOTIFY]])->andWhere([">", "created_at", time() - 900])->orderBy("id desc")->select("id")->scalar();
        if ($oid > 0)
            return $this->send(["oid" => intval($oid)], "存在未支付订单");
        $order = new Order;
        $order->tid = $p->tid;
        $order->pid = $pid;
        $order->title = $p->title;
        $order->cover = empty($p->cover) ? $type->icon : $p->cover;
        $order->uid = $this->user_id();
        $order->openId = $this->getUser()->openId;
//        $order->formId = $this->getPost("formId");
        $order->price = $p->price;
        $order->hour = $p->hour;
        $order->status = Status::WAIT_PAY;
        $order->created_at = time();
        if ($order->save())
            return Tool::reJson(["oid" => $order->attributes['id']]);
        else {
            Yii::warning($order->errors, "新建Order失败");
            return Tool::reJson(null, "下单失败,请重试", Tool::FAIL);
        }
    }
}