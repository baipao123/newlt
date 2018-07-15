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
        if (!$order)
            return 0;
        $res = $order->wxQuery();
        if (!$res) {
            $queue->delay(150)->push(new CloseOrder([
                "id" => $this->id
            ]));
            return 1;
        } else
            return 0;
    }
}