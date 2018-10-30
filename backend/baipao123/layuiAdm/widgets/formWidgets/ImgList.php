<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午8:40
 */

namespace layuiAdm\widgets\formWidgets;

use layuiAdm\widgets\QiNiuUploaderWidget;

class ImgList extends QiNiuUploaderWidget
{
    public $name;

    public $value;

    public function run() {

        return $this->renderFile(dirname(__FILE__) . '/../../views/qiniu/imgList.php', [
            "uploader" => parent::run(),
            "id"       => $this->getId(),
            "multi"    => $this->isMulti,
            "isDelete" => $this->isDelete,
            "name"     => $this->name,
            "img"      => $this->getValues()
        ]);
    }

    protected function getValues() {
        if (empty($this->value))
            return [];
        if ($this->isMulti) {
            return json_decode($this->value, true);
        }
        return [$this->value];
    }
}