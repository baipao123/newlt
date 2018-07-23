<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:48
 */

namespace backend\controllers;

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

    }

    public function actionQuery($oid, $out_trade_no, $trade_no) {

    }

    public function actionRefund() {

    }
}