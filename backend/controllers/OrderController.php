<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:48
 */

namespace backend\controllers;

use common\tools\WxPay;
use yii;
use common\models\Order;
use yii\data\Pagination;

class OrderController extends BaseController
{
    public $basicActions = ["info", "refund"];

    public function actionList($uid = 0, $tid = 0, $pid = 0, $status = 0) {
        $query = Order::find();
        if (!empty($uid))
            $query->andWhere(["uid" => $uid]);
        if (!empty($tid))
            $query->andWhere(["tid" => $tid]);
        if (!empty($pid))
            $query->andWhere(["pid" => $pid]);
        if (!empty($status))
            $query->andWhere(["status" => $status]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $list = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->with(["user", "qType", "qPrice"])->orderBy("id desc")->all();
        return $this->render("list", [
            "list"       => $list,
            "pagination" => $pagination,
            "uid"        => $uid,
            "tid"        => $tid,
            "pid"        => $pid,
            "status"     => $status,
        ]);
    }

    public function actionInfo($oid) {
        $order = Order::findOne($oid);
        if (!$order)
            return $this->alert("未找到订单");

        return $this->render("info", [
            "order" => $order
        ]);
    }

    public function actionQuery($oid = 0, $out_trade_no = "", $trade_no = "") {
        $params = [];
        if (!empty($oid))
            $params['id'] = $oid;
        if (!empty($out_trade_no))
            $params['out_trade_no'] = $out_trade_no;
        if (!empty($trade_no))
            $params['trade_no'] = $trade_no;
        if (empty($params))
            return $this->render("query", [
                "oid"          => $oid,
                "out_trade_no" => $out_trade_no,
                "trade_no"     => $trade_no,
                "order"        => null,
                "data"         => false
            ]);
        $order = Order::findOne($params);
        if (empty($out_trade_no) && empty($trade_no)) {
            if (!$order)
                return $this->render("query", [
                    "oid"          => $oid,
                    "out_trade_no" => $out_trade_no,
                    "trade_no"     => $trade_no,
                    "order"        => null,
                    "data"         => false
                ]);
            $out_trade_no = $order->out_trade_no;
        }
        $query = WxPay::getInstance()->Query($out_trade_no, $trade_no);
        return $this->render("query", [
            "oid"          => $oid,
            "out_trade_no" => $out_trade_no,
            "trade_no"     => $trade_no,
            "order"        => $order,
            "data"         => $query
        ]);
    }

    public function actionRefund($oid, $price) {

    }
}