<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:51:57
 */

namespace frontend\controllers;

use common\tools\Status;
use common\tools\Tool;
use Yii;
use common\models\Order;

/**
 * @property Order $order;
 */
class OrderController extends BaseController
{
    protected $order;

    public function actionInfoForPay($oid) {
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return Tool::reJson(null, "订单不存在", Tool::FAIL);
        if ($order->status == Status::CANCEL_PAY)
            return Tool::reJson(null, "订单已超时，无法支付", Tool::FAIL);
        if (in_array($order->status, [Status::IS_PAY, Status::IS_REFUND]))
            return Tool::reJson(null, "订单已成功支付", Tool::FAIL);

        return Tool::reJson(["info" => $order->info()]);
    }

    public function actionPay($oid) {

    }

    public function actionRecord($type = 0) {

    }

    public function actionInfo($oid) {

    }
}