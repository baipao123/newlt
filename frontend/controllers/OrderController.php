<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:51:57
 */

namespace frontend\controllers;

use common\tool\WxPay;
use common\tools\Status;
use common\tools\Tool;
use Yii;
use common\models\Order;
use yii\helpers\ArrayHelper;

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
        if (in_array($order->status, [Status::IS_PAY, Status::IS_REFUND]))
            return Tool::reJson(null, "订单已成功支付", Tool::FAIL);
        if ($order->status == Status::CANCEL_PAY || $order->created_at + 900 <= time())
            return Tool::reJson(null, "订单已超时，无法支付", Tool::FAIL);
        $info = [
            "oid"        => $order->id,
            "title"      => $order->title,
            "cover"      => $order->cover,
            "price"      => $order->price,
            "created_at" => $order->created_at
        ];

        return Tool::reJson(["info" => $info]);
    }

    public function actionPay($oid) {
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return Tool::reJson(null, "订单不存在", Tool::FAIL);
        if (in_array($order->status, [Status::IS_PAY, Status::IS_REFUND]))
            return Tool::reJson(null, "订单已成功支付", Tool::FAIL);
        if ($order->status == Status::CANCEL_PAY || $order->created_at + 900 <= time())
            return Tool::reJson(null, "订单已超时，无法支付", Tool::FAIL);

        $redisKey = "DK-PAY-UID:" . $this->user_id();
        if (Yii::$app->redis->setnx($redisKey, 1) == 0)
            return Tool::reJson(null, "3秒内只允许支付一次，请稍后重试", Tool::FAIL);

        $order->formId = $this->getPost("formId");
        $order->save();

        Yii::$app->redis->expire($redisKey, 3);
        $params = $order->wxPayParams();
        if ($params === false)
            return Tool::reJson(null, WxPay::getInstance()->getError(), Tool::FAIL);
        return Tool::reJson(["params" => $params]);
    }

    public function actionPaySuccess() {
        $oid = $this->getPost("oid", 0);
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return Tool::reJson(null, "订单不存在", Tool::FAIL);
        if ($order->status == Status::IS_UNIFY_ORDER && Yii::$app->redis->setnx(Order::NotifyRedisKey . $order->id, 1)) {
            $order->status = Status::WAIT_NOTIFY;
            $order->save();
        }
        return Tool::reJson(null);
    }

    public function actionPayQuery($oid) {
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return Tool::reJson(null, "订单不存在", Tool::FAIL);
        if (in_array($order->status, [Status::IS_PAY, Status::IS_UNIFY_ORDER]))
            return Tool::reJson(["info" => $order->info()]);
        $query = WxPay::getInstance()->Query($order->out_trade_no);
        if($query === false)
            return Tool::reJson(null,WxPay::getInstance()->getError(),Tool::FAIL);
        if ($query['trade_state'] != "SUCCESS") {
            $arr = [
                "SUCCESS"    => "支付成功",
                "REFUND"     => "转入退款",
                "NOTPAY"     => "未支付",
                "CLOSED"     => "已关闭",
                "REVOKED"    => "已撤销",
                "USERPAYING" => "用户支付中",
                "PAYERROR"   => "支付失败"
            ];
            return Tool::reJson(null, "订单" . ArrayHelper::getValue($arr, $query['trade_state'], "支付失败"), Tool::FAIL);
        }
        if ($query['total_fee'] == $order->price && $query['openid'] == $order->openId) {
            $order->status = Status::IS_PAY;
            $order->trade_no = $query['transaction_id'];
            $order->payat = $query['time_end'];
            $order->afterPay();
            $order->save();
            return Tool::reJson(["info" => $order->info()]);
        }
        return Tool::reJson(null, "订单支付异常", Tool::FAIL);
    }

    public function actionRecord($type = 0) {

    }

    public function actionInfo($oid) {

    }
}