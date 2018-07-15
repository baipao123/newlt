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
        return $this->send(["info" => $order->info(), "user" => $order->status == Status::IS_PAY ? $this->getUser()->info() : null]);
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
        $order->payat = time();
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
        $res = $order->wxQuery();
        return $this->send([
            "info" => $order->info(),
            "user" => $order->status == Status::IS_PAY ? $this->getUser()->info() : null
        ], !$res ? WxPay::getInstance()->getError() : "");
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