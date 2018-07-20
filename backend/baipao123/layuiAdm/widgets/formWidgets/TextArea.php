<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午7:59
 */

namespace layuiAdm\widgets\formWidgets;


class TextArea extends InputOptions
{
    public $defaultClasses = ['layui-textarea'];

    public $needValue = false;

    public function run() {
        return '<textarea' . self::generateOptions($this->config()) . '>' . $this->value . '</textarea>';
    }
}