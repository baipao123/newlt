<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/11/011
 * Time: 17:13
 */

namespace common\tool;

use common\tools\StringHelper;
use yii;

class WxPay extends WxPayBase
{
    const URL_HEAD = 'https://api.mch.weixin.qq.com/';//正式环境
    //    const URL_HEAD='https://api.mch.weixin.qq.com/sandbox/';//测试沙箱，不发生财务变化

    protected $notify_end = "/wx/notify";

    public static $_instance;


    public function __construct() {
        $this->wxPay = Yii::$app->params['wxPay'];
    }

    // 获取单例
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $method
     * @param array $params
     * @param bool $useCert
     * @param int $timeOut
     * @return array|bool 微信正常返回的部分
     * 微信正常返回的部分
     * report  如果请求不正确，那么会存在这个字段
     * out_trade_no
     * trade_no
     * params  string  post请求的xml
     */
    public function Post($method, $params = [], $useCert = false, $timeOut = 6) {
        $params['sign'] = $this->MakeSign($params);
        $xml = $this->ToXml($params);
        $startTimeStamp = $this->getMillisecond();
        $url = self::URL_HEAD . $method;
        $response = $this->postXmlCurl($xml, $url, $useCert, $timeOut);
        if ($response === false)
            return false;
        if (isset($params['out_trade_no']))
            $response['out_trade_no'] = $params['out_trade_no'];
        if (isset($params['transaction_id']))
            $response['trade_no'] = $params['transaction_id'];
        $res = $this->report($url, $startTimeStamp, $response);
        $response['params'] = $xml;
        if ($res) {
            Yii::warning($response, "微信错误-" . $method);
            $response['report'] = $res;
            if (isset($response['return_code'])) {
                if ($response['return_code'] != "SUCCESS")
                    $this->setResponseError($response['return_msg']);
                elseif (isset($res["err_code_des"]))
                    $this->setResponseError($response['err_code_des']);
            }
        }
        return $response;
    }

    /**
     *统一下单, 下单成功后，返回调起支付所需的信息
     * @param $data array  商品的信息  包括body、detail、out_trade_no、total_fee。。。
     * @return array|bool
     * @throws yii\base\ExitException
     */
    public function UnifiedOrder($data) {
        $notify_url = rtrim(Yii::$app->params['api_url'], "/") . $this->notify_end;
        $params = [
            'appid'            => $this->wxPay['appid'],
            'mch_id'           => $this->wxPay['mchid'],
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url'       => $notify_url,
            'trade_type'       => 'JSAPI',
            'nonce_str'        => $this->getNonceStr(),
        ];
        if (isset($data['body']))
            $data['body'] = StringHelper::formatTitleForPay($data['body'], 40, 128);
        $params = array_merge($params, $data);
        $response = $this->Post("pay/unifiedorder", $params);
        if ($response === false || isset($response['report']) || !empty($this->getError()))
            return false;
        return $response['prepay_id'];
    }

    // 获取支付需要的参数
    public function getPayParams($prepay_id) {
        $data = [
            'appId'     => $this->wxPay['appid'],
            'package'   => "prepay_id=" . $prepay_id,
            'nonceStr'  => $this->getNonceStr(),
            'timeStamp' => (string)time(),
            'signType'  => 'MD5'// 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
        ];
        $data['paySign'] = $this->MakeSign($data);
        unset($data['appId']);
        return $data;
    }

    /**
     * @param string $trade_no
     * @param string $out_trade_no
     * @param string $refund_no
     * @param int $total_fee
     * @param int $refund_fee
     * @param int $refund_type 0 :未结算资金退款，1：可用余额退款
     * @param int $timeOut
     * @return bool|mixed
     */
    public function Refund($trade_no = '', $out_trade_no = '', $refund_no = '', $total_fee = 0, $refund_fee = 0, $refund_type = 0, $timeOut = 6) {
        if (empty($refund_no))
            $refund_no = $this->getNonceStr(32);
        $params = [
            'appid'          => $this->wxPay['appid'],
            'mch_id'         => $this->wxPay['mchid'],
            'nonce_str'      => $this->getNonceStr(),
            'out_refund_no'  => $refund_no,
            'total_fee'      => $total_fee,
            'refund_fee'     => $refund_fee,
            'op_user_id'     => $this->wxPay['mchid'],
            'refund_account' => $refund_type == 0 ? "REFUND_SOURCE_UNSETTLED_FUNDS" : "REFUND_SOURCE_RECHARGE_FUNDS",//使用未结算资金退款，还是可用余额退款
        ];
        if (!empty($trade_no))
            $params['transaction_id'] = $trade_no;
        else if (!empty($out_trade_no))
            $params['out_trade_no'] = $out_trade_no;
        else
            return false;
        return $this->Post("secapi/pay/refund", $params, true);
    }

    /**
     * 查询订单
     * @param string $transaction_id
     * @param string $out_trade_no
     * @return mixed
     */
    public function Query($out_trade_no = '', $transaction_id = '') {
        $params = [
            'appid'     => $this->wxPay['appid'],
            'mch_id'    => $this->wxPay['mchid'],
            'nonce_str' => $this->getNonceStr(),
        ];
        if (!empty($transaction_id))
            $params['transaction_id'] = $transaction_id;
        else if (!empty($out_trade_no))
            $params['out_trade_no'] = $out_trade_no;
        else
            return false;
        $response = $this->Post("pay/orderquery", $params);
        return $response === false || isset($response['report']) ? false : $response;
    }

    /**
     * 关闭订单
     * @param string $out_trade_no
     * @param int $timeOut
     * @return mixed
     */
    public function CloseOrder($out_trade_no, $timeOut = 6) {
        $params = [
            'appid'     => $this->wxPay['appid'],
            'mch_id'    => $this->wxPay['mchid'],
            'nonce_str' => $this->getNonceStr(),
        ];
        if (!empty($out_trade_no))
            $params['out_trade_no'] = $out_trade_no;
        else
            return false;
        $response = $this->Post("pay/closeorder", $params);
        return $response === false || isset($response['report']) ? false : $response;
    }

    public function RefundQuery($out_trade_no = '', $transaction_id = '', $timeOut = 6) {
        $params = [
            'appid'     => $this->wxPay['appid'],
            'mch_id'    => $this->wxPay['mchid'],
            'nonce_str' => $this->getNonceStr(),
        ];
        if (!empty($transaction_id))
            $params['transaction_id'] = $transaction_id;
        else if (!empty($out_trade_no))
            $params['out_trade_no'] = $out_trade_no;
        else
            return false;
        $response = $this->Post("pay/refundquery", $params);
        return $response === false || isset($response['report']) ? false : $response;
    }
}