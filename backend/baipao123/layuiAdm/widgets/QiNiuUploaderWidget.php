<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/13
 * Time: 下午7:20
 */

namespace layuiAdm\widgets;

use Yii;

class QiNiuUploaderWidget extends Widget
{
    public $callBack = "uploadFile";

    public $isMulti = true;

    public $tokenUrl = "./qiniu-token";

    public $mineTypes = ["image/png", "image/jpeg", "image/gif"];

    public $ext = []; //额外字段

    public $useCdn = true;

    public $region = "";

    public function init() {
        $this->assetFiles = [
            "/qiniu/qiniu.min.js",
            "/qiniu/qiniu.token.js"
        ];
        // 华东区
        if (empty($this->region) && isset(Yii::$app->params['qiniu']) && isset(Yii::$app->params['qiniu']['region']))
            $this->region = Yii::$app->params['qiniu']['region'];
        parent::init();
    }

    public function run() {
        parent::run();
        $params = "{";
        foreach ($this->ext as $key => $val) {
            $params .= $key . ":" . json_encode($val);
        }
        $params .= "}";
        return $this->renderFile(dirname(__FILE__) . '/../views/qiniu/uploader.php', [
            'id'       => $this->id,
            'isMulti'  => $this->isMulti,
            'callBack' => $this->callBack,
            'mineTypes'=> empty($this->mineTypes) ? "null" : '["' . implode('", "', $this->mineTypes) . '"]',
            'params'   => $params,
            'useCdn'   => $this->useCdn ? "true" : "false",
            'tokenUrl' => $this->tokenUrl,
            'region'   => $this->region,
        ]);
    }
}