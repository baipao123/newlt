<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 17:23:24
 */

namespace common\models;

use Yii;
use common\tool\WxPay;

/**
 * @property User $user
 */
class Order extends \common\models\base\Order
{
    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }


    public function info(){

    }

    public function wxPay() {
        $data = [
            "openId"       => $this->user->openId,
            "body"         => $this->title,
            "out_trade_no" => $this->out_trade_no,
            "total_fee"    => $this->price
        ];
        return WxPay::getInstance()->UnifiedOrder($data);
    }

    public function wxRefund(){

    }

    public function wxQuery(){

    }

    public function afterPay(){

    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert || isset($changedAttributes['status']))
            OrderStatus::saveRecord($this->id, $this->status);
    }
}