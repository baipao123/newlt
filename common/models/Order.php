<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 17:23:24
 */

namespace common\models;

use common\tools\Status;
use common\tools\StringHelper;
use Yii;
use common\tool\WxPay;

/**
 * @property User $user
 */
class Order extends \common\models\base\Order
{
    const NotifyRedisKey = "WX-NOTIFY-OID:";

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }


    public function info(){
        return [
            "oid" => $this->id,
            "openId" => $this->openId,
            "title" => $this->title,
            "cover" => $this->cover,
            "price" => $this->price,
        ];
    }

    public function wxPayParams() {
        $prepayId = $this->getPrepayId();
        if ($prepayId === false)
            return false;
        $data = [
            'signType'  => "MD5",
            'package'   => $this->getPrepayId(),
            'nonceStr'  => StringHelper::nonce(8),
            'timestamp' => (string)time(),
        ];
        $data['paySign'] = WxPay::getInstance()->MakeSign($data);
        return $data;
    }

    public function getPrepayId() {
        if (!empty($this->prepay_id))
            return $this->prepay_id;
        $data = [
            "openId"       => $this->openId,
            "body"         => $this->title,
            "out_trade_no" => $this->out_trade_no,
            "total_fee"    => $this->price,
            'time_start'   => date("YmdHis", $this->created_at),
            'time_expire'  => date("YmdHis", $this->created_at + 900),
        ];
        $response = WxPay::getInstance()->UnifiedOrder($data);
        if (!$response || !isset($response['prepay_id']))
            return false;
        $prepayId = $response['prepay_id'];
        $this->prepay_id = $prepayId;
        $this->status = Status::IS_UNIFY_ORDER;
        $this->save();
        return $prepayId;
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

    public static function getOutTradeNo() {
        return (string)intval(microtime(true) * 1000) . (string)mt_rand(10000, 99999);
    }
}