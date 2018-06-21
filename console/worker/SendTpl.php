<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/21
 * Time: 下午7:51
 */

namespace console\worker;


use common\tools\WxApp;

class SendTpl extends BaseJob
{
    public $openId;

    public $type;

    public $data;

    public $color;

    public $page;

    public $formId;

    public $keyword;

    public function execute($queue) {
        $tplData = [];
        for ($j = 1; $j <= count($this->data); $j++) {
            $tplData[ "keyword" . $j ] = [
                "value" => $this->data[ $j - 1 ]
            ];
            if (isset($this->color[ $j ]))
                $tplData[ "keyword" . $j ]['color'] = $this->color[ $j ];
        }
        $accessToken = WxApp::getAccessToken();
        WxApp::sendTpl($accessToken, $this->openId, $this->type, $tplData, $this->page, $this->formId, $this->keyword);
    }
}