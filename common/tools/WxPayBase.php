<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1/001
 * Time: 16:22
 */
namespace common\tools;

use yii;

class WxPayBase
{
    // 微信支付的配置信息
    public $wxPay;

    /*
     * 微信支付的基础类，主要用于$this->values储存要传入的参数，setsign制作签名，访问接口，错误上报
     *
     * Yii中params解释
     * =======【基本信息设置】==========
     * appid            申请微信支付的appid
     * mchid            商户号
     * key              商户中的关键key
     *
     * =======【证书路径设置】========
     * ssl_cert_path    微信商户平台下载的证书在服务器的路径
     * ssl_key_path
     */

    /**
     * =======【回调地址】========
     * 所有的回调地址，不带域名，使用时前面需要加上域名
     */
    const PAY_NOTIFY_END = '/wx/notify';
    /**
     * =======【上报信息配置】=========
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    const REPORT_LEVEL = 1;

    protected $values = [];

    protected $curl_error_no = "";
    protected $curl_error_msg = "";
    protected $curl_response_error = "";

    protected function resetCurlError() {
        $this->curl_response_error = "";
        $this->curl_error_msg = "";
        $this->curl_error_no = "";
    }

    public function getError($default = "") {
        if (!empty($this->curl_response_error))
            return $this->curl_response_error;
        if (!empty($this->curl_error_msg))
            return $this->curl_error_no . " : " . $this->curl_error_msg;
        return $default;
    }

    public function setResponseError($text = "") {
        $this->curl_response_error = $text;
    }

    /**
     * 生成签名
     * @param array $arr
     * @return string 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($arr) {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        $string = $this->ToUrlParams($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->wxPay['key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return
     **/
    public function GetSign() {
        return $this->values['sign'];
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string 产生的随机字符串
     */
    public function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 格式化参数格式化成url参数
     * @param array $arr
     * @return string
     */
    public function ToUrlParams($arr) {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 输出xml字符
     * @param array $arr
     * @return string
     */
    public function ToXml($arr) {
        if (!is_array($arr) || count($arr) <= 0)
            return false;
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return array
     */
    public function FromXml($xml) {
        if (!$xml || is_array($xml))
            return false;
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @return mixed|void
     */
    public function postXmlCurl($xml, $url, $useCert = false, $second = 6) {
        $this->resetCurlError();

        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //        curl_setopt($ch, CURLOPT_CAINFO,$this->wxPay['ca_cert_path']);//证书地址
        //方案一：删除掉指定根证书的代码。当程序中不指定根证书时，会使用系统自带的根证书。绝大部分系统中已内置了微信支付的根证书，所以删除掉指定根证书的代码，不会影响到你的现有业务。
        //方案二：更新根证书。往truststore或者根证书信任文件中追加新的根证书（注意：追加新的根证书时，需要保留老的根证书）。
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            $ssl_cert_path = $this->wxPay['ssl_cert_path'];
            $ssl_key_path = $this->wxPay['ssl_key_path'];
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $ssl_cert_path);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $ssl_key_path);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //        Yii::warning(curl_error($ch), "微信支付-curl:" . curl_errno($ch));
        //返回结果
        if ($data) {
            curl_close($ch);
            $data = self::FromXml($data);
            return $data;
        } else {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            Yii::warning("$errno:$error", "wx_pay_curl出错");
            $this->curl_error_no = $errno;
            $this->curl_error_msg = $error;
            return false;
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    public function getMillisecond() {
        //获取毫秒的时间戳
        return sprintf("%.3f", microtime(true)) * 1000;
    }

    /**
     * 微信测速上报，
     * @param $interface_url  string  使用的接口url
     * @param $startTimeStamp int     接口开始时间戳，毫秒级
     * @param $data          array    接口返回的数据
     * @return mixed|false
     */
    public function report($interface_url, $startTimeStamp, $data) {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        if (self::REPORT_LEVEL == 0)
            return false;
        if (self::REPORT_LEVEL == 1 &&
            array_key_exists('return_code', $data) &&
            array_key_exists('result_code', $data) &&
            $data['return_code'] == 'SUCCESS' &&
            $data['result_code'] == 'SUCCESS'
        )
            return false;
        $endTimeStamp = self::getMillisecond();
        $params = [
            'appid'         => $this->wxPay['appid'],
            'mch_id'        => $this->wxPay['mchid'],
            'user_ip'       => yii\helpers\ArrayHelper::getValue($_SERVER, "REMOTE_ADDR"),
            'time'          => date('YmdHis'),
            'nonce_str'     => $this->getNonceStr(),
            'interface_url' => $interface_url,
            'execute_time_' => $endTimeStamp - $startTimeStamp,
        ];
        if (array_key_exists('return_code', $data))
            $params['return_code'] = $data['return_code'];
        else
            $params['return_code'] = 'FAIL';
        if (array_key_exists('result_code', $data))
            $params['result_code'] = $data['result_code'];
        else
            $params['result_code'] = 'FAIL';
        if (array_key_exists('return_msg', $data))
            $params['return_msg'] = $data['return_msg'];
        if (array_key_exists('err_code', $data))
            $params['err_code'] = $data['err_code'];
        if (array_key_exists('err_code_des', $data))
            $params['err_code_des'] = $data['err_code_des'];
        if (array_key_exists('out_trade_no', $data))
            $params['out_trade_no'] = $data['out_trade_no'];
        if (array_key_exists('device_info', $data))
            $params['device_info'] = $data['device_info'];
        $params['sign'] = $this->MakeSign($params);
        $xml = $this->ToXml($params);
        $response = $this->postXmlCurl($xml, $url);
        return $response;
    }
}