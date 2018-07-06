<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/21
 * Time: 下午7:34
 */

namespace console\worker;


use common\models\Order;
use common\tools\Status;

class CloseOrder extends BaseJob
{
    public $id;

    public function execute($queue) {
        $order = Order::findOne($this->id);
        if (!$order || in_array($order->status, [Status::IS_PAY, Status::IS_REFUND, Status::CANCEL_PAY]))
            return 0;
        $query = $order->wxQuery();
        if ($query) {
            $order->status = Status::IS_PAY;
            $order->save();
            $order->afterPay();
            return 0;
        } else {
            $order->status = Status::CANCEL_PAY;
            $order->save();
            return 0;
        }
    }
}