<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:34:46
 */

namespace frontend\controllers;

use common\models\OrderNotify;
use common\tool\WxPay;
use common\tools\Status;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class WxController extends Controller
{
    public function actionNotify() {
        $xml = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");
        $wxPay = new WxPay;
        $params = $wxPay->FromXml($xml);
        $out_trade_no = ArrayHelper::getValue($params, "out_trade_no");
        $record = new OrderNotify;
        $record->params = json_encode($params);
        $record->out_trade_no = $out_trade_no;
        $record->created_at = time();
        $record->status = OrderNotify::StatusWaitVerify;
        $record->save();

        $sign = $wxPay->MakeSign($params);
        $sign2 = ArrayHelper::getValue($params, "sign");
        if ($sign != $sign2) {
            $record->status = OrderNotify::StatusVerifyFail;
            $record->save();
            return false;
        }
        if (ArrayHelper::getValue($params, "appid") != $wxPay->wxPay['appid']) {
            $record->status = OrderNotify::StatusAppidError;
            $record->save();
            return false;
        }
        $order = Order::findOne(["out_trade_no" => $out_trade_no]);
        if(!$order){
            $record->status = OrderNotify::StatusOrderNotFound;
            $record->save();
            return false;
        }

        if($order->price != ArrayHelper::getValue($params, "total_fee")){
            $record->status = OrderNotify::StatusOrderPriceError;
            $record->save();
            return false;
        }

        $record->status = OrderNotify::StatusVerifyPass;
        $record->save();

        $order->status = Status::IS_PAY;
        $order->trade_no = ArrayHelper::getValue($params, "trade_no");
        $order->paytime = ArrayHelper::getValue($params, "paytime");
        $order->save();

        if ($order->afterPay())
            echo $wxPay->ToXml([
                "return_code" => "SUCCESS",
                "return_msg"  => "OK"
            ]);
        return 0;
    }
}