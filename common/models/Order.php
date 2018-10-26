<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 17:23:24
 */

namespace common\models;

use common\tools\Status;
use yii;
use common\tools\WxPay;
use yii\helpers\ArrayHelper;

/**
 * @property User $user
 * @property QuestionPrice $qPrice
 * @property QuestionType $qType
 */
class Order extends \common\models\base\Order
{
    const NotifyRedisKey = "WX-NOTIFY-OID:";

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

    public function getQType() {
        return $this->hasOne(QuestionType::className(), ["id" => "tid"]);
    }

    public function getQPrice() {
        return $this->hasOne(QuestionPrice::className(), ["id" => "pid"]);
    }

    public function info() {
        $info = [
            "oid"          => $this->id,
            "title"        => $this->title,
            "status"       => $this->status,
            "cover"        => $this->cover,
            "price"        => $this->price,
            "out_trade_no" => $this->out_trade_no,
            "created_at"   => date("Y-m-d H:i:s", $this->created_at)
        ];
        if (in_array($this->status, [Status::IS_PAY, Status::IS_REFUND])) {
            $info = ArrayHelper::merge($info, [
                "trade_no" => $this->trade_no,
                "pay_at"   => $this->paytime
            ]);
        } else {
            $info['expire_at'] = $this->created_at + 900;
        }
        return $info;
    }

    public function wxPayParams() {
        $prepayId = $this->getPrepayId();
        if ($prepayId === false)
            return false;
        return WxPay::getInstance()->getPayParams($prepayId);
    }

    public function getPrepayId() {
        if (!empty($this->prepay_id))
            return $this->prepay_id;
        //跨时区大作战，不然就是X小时15分钟的支付时间
        $timeZone = date_default_timezone_get();
        date_default_timezone_set("PRC");
        $data = [
            "openid"       => $this->openId,
            "body"         => $this->title,
            "out_trade_no" => $this->out_trade_no,
            "total_fee"    => $this->price,
            'time_start'   => date("YmdHis", $this->created_at),
            'time_expire'  => date("YmdHis", $this->created_at + 900),
        ];
        date_default_timezone_set($timeZone);
        $response = WxPay::getInstance()->UnifiedOrder($data);
        if (!$response)
            return false;
        $prepayId = $response;
        $this->prepay_id = $prepayId;
        $this->status = Status::IS_UNIFY_ORDER;
        $this->save();
        return $prepayId;
    }

    // false 需要下次再查询，true 则处理成功
    public function wxQuery() {
        if (!in_array($this->status, [Status::WAIT_NOTIFY, Status::IS_UNIFY_ORDER])) {
            if ($this->status == Status::WAIT_PAY) {
                $this->status = Status::CANCEL_PAY;
                $this->save();
            }
            return true;
        }
        $query = WxPay::getInstance()->Query($this->out_trade_no);
        if ($query === false)
            return false;
        //        $arr = [
        //            "SUCCESS"    => "支付成功",
        //            "REFUND"     => "转入退款",
        //            "NOTPAY"     => "未支付",
        //            "CLOSED"     => "已关闭",
        //            "REVOKED"    => "已撤销",
        //            "USERPAYING" => "用户支付中",
        //            "PAYERROR"   => "支付失败"
        //        ];
        if (!isset($query['trade_state']))
            return false;
        if ($query['trade_state'] == "NOTPAY" && $this->created_at + 900 > time())
            return false;
        else if ($query['trade_state'] == "USERPAYING")
            return false;
        else if ($query['trade_state'] == "REFUND") {
            $this->status = Status::IS_REFUND;
            $this->trade_no = $query['transaction_id'];
            $this->payat = $query['time_end'];
            $this->afterPay();
            $this->save();
            return true;
        } else if ($query['trade_state'] == "SUCCESS") {
            $this->status = Status::IS_PAY;
            $this->trade_no = $query['transaction_id'];
            $this->payat = $query['time_end'];
            $this->afterPay();
            $this->save();
            return true;
        } else {
            $this->status = Status::CANCEL_PAY;
            $this->save();
            return true;
        }
    }


    public function refund($cash = 0) {
        $cash = $cash ?: $this->price;
        $record = OrderRefundRecord::findOne(["out_trade_no" => $this->out_trade_no]);
        $refundNo = $record ? $record->refund_no : self::generateOutTradeNo();
        return $this->wxRefund($refundNo, $cash);
    }

    private function wxRefund($refundNo, $cash) {
        $cash = $cash ?: $this->price;
        $response = WxPay::getInstance()->Refund("", $this->out_trade_no, $refundNo, $this->price, $cash);
        if (!$response)
            return false;
        if (isset($response['return_code']) && $response['return_code'] == 'NOTENOUGH') {
            $response = WxPay::getInstance()->Refund("", $this->out_trade_no, $refundNo, $this->price, $cash, 1);
            return !$response || isset($response['report']) ? false : true;
        }
        return isset($response['report']) ? false : true;
    }

    public function afterPay() {
        if ($this->status != Status::IS_PAY)
            return true;
        $expire_at = UserQuestionType::find()->where(["tid" => $this->tid, "uid" => $this->uid])->orderBy("expire_at DESC")->limit(1)->select("expire_at")->scalar();
        $record = new UserQuestionType;
        $record->uid = $this->uid;
        $record->tid = $this->tid;
        $record->oid = $this->id;
        $record->created_at = time();
        $record->expire_at = max(time(), $expire_at) + $this->hour * 3600;
        $record->save();
        $user = $this->user;
//        if ($user->tid == 0 || $user->tid == $this->tid || $user->expire_at <= time()) {
            $user->expire_at = $record->expire_at;
//            if ($user->tid == 0) {
//                $types = QuestionType::getList($this->tid);
//                if (count($types) == 1) {
//                    $user->tid2 = $types[0]->id;
//                }
//            }
//            $user->tid = $this->tid;
            $user->save();
//        }
        //        $user->sendTplByQueue();
        return true;
    }

    public function beforeSave($insert) {
        if (empty($this->out_trade_no))
            $this->out_trade_no = self::generateOutTradeNo();
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert || isset($changedAttributes['status']))
            OrderStatus::saveRecord($this->id, $this->status);
    }

    public static function generateOutTradeNo() {
        return (string)intval(microtime(true) * 1000) . (string)mt_rand(10000, 99999);
    }
}