<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:51:57
 */

namespace frontend\controllers;

use common\tools\WxPay;
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

    public function actionInfo() {
        $oid = $this->getPost("oid", 0);
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return $this->sendError("订单不存在");
        return $this->send(["info" => $order->info()]);
    }

    public function actionPay() {
        $oid = $this->getPost("oid", 0);
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return $this->sendError("订单不存在");
        if (in_array($order->status, [Status::IS_PAY, Status::IS_REFUND]))
            return $this->sendError("订单已成功支付");
        if ($order->status == Status::CANCEL_PAY || $order->created_at + 900 <= time())
            return $this->sendError("订单已超时，无法支付");

        $redisKey = "DK-PAY-UID:" . $this->user_id();
        if (Yii::$app->redis->setnx($redisKey, 1) == 0)
            return $this->sendError("3秒内只允许支付一次，请稍后重试");
        Yii::$app->redis->expire($redisKey, 3);

        $order->formId = $this->getPost("formId");
        $order->save();
        $params = $order->wxPayParams();
        if ($params === false)
            return $this->sendError(WxPay::getInstance()->getError());
        return $this->send(["params" => $params]);
    }

    public function actionPaySuccess() {
        $oid = $this->getPost("oid", 0);
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return $this->sendError("订单不存在");
        if ($order->status == Status::IS_UNIFY_ORDER && Yii::$app->redis->setnx(Order::NotifyRedisKey . $order->id, 1)) {
            $order->status = Status::WAIT_NOTIFY;
            $order->save();
        }
        return $this->send(["info" => $order->info()]);
    }

    public function actionQuery() {
        $oid = $this->getPost("oid", 0);
        $order = Order::findOne($oid);
        if (!$order || $order->uid != $this->user_id())
            return $this->sendError("订单不存在");
        if (in_array($order->status, [Status::IS_PAY, Status::IS_UNIFY_ORDER]))
            return $this->send(["info" => $order->info()]);
        $query = WxPay::getInstance()->Query($order->out_trade_no);
        if ($query === false)
            return $this->sendError(WxPay::getInstance()->getError());
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
            return $this->sendError("订单" . ArrayHelper::getValue($arr, $query['trade_state'], "支付失败"));
        }
        if ($query['total_fee'] == $order->price && $query['openid'] == $order->openId) {
            $order->status = Status::IS_PAY;
            $order->trade_no = $query['transaction_id'];
            $order->payat = $query['time_end'];
            $order->afterPay();
            $order->save();
            return $this->send(["info" => $order->info()]);
        }
        return $this->sendError("订单支付异常");
    }

    public function actionRecord($page = 1, $limit = 10) {
        $orders = Order::find()->where(["uid" => $this->user_id()])->offset(($page - 1) * $limit)->limit($limit)->orderBy("id desc")->all();
        /* @var $orders Order[] */
        $data = [];
        foreach ($orders as $order) {
            $data[] = $order->info();
        }
        return $this->send(["list" => $data]);
    }
}