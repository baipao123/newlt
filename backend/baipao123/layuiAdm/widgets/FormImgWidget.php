<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/19
 * Time: 下午6:31
 */

namespace layuiAdm\widgets;


class FormImgWidget extends FormBaseWidget
{
    public $callBack = "uploadFile";

    public $isMulti = true;

    public $tokenUrl = "./qiniu-token";

    public $mineTypes = ["image/png", "image/jpeg", "image/gif"];

    public $ext = []; //额外字段

    public $useCdn = true;

    public $region = "";

    public $hint = "";


    public function run() {
        $html = $this->itemHead();

        $html .= QiNiuUploaderWidget::widget([
            "callBack"  => $this->callBack,
            "isMulti"   => $this->isMulti,
            "tokenUrl"  => $this->tokenUrl,
            "mineTypes" => $this->mineTypes,
            "ext"       => $this->ext,
            "useCdn"    => $this->useCdn,
            "region"    => $this->region,
            "hint"      => $this->hint
        ]);

        $html .= $this->imgList();
        $html .= $this->itemEnd();
        return $html;
    }

    public function imgList() {
        return '';
    }
}