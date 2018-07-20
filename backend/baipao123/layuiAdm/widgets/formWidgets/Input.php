<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午7:58
 */

namespace layuiAdm\widgets\formWidgets;


class Input extends InputOptions
{
    public $defaultClasses = ['layui-input'];

    public function run() {
        return '<input' . self::generateOptions($this->config()) . '/>';
    }
}