<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/18
 * Time: 下午10:27
 */

namespace layuiAdm\widgets;

use yii\helpers\ArrayHelper;

class InputWidget extends Widget
{
    public $type = "text";
    public $name;
    public $value;

    public $placeholder;

    public $options;

    public $defaultClasses = ['layui-input'];
    public $classes;

    public $inputId;

    public $verify;
    public $filter;

    public function run() {
        $data = [
            "type"  => $this->type,
            "name"  => $this->name,
            "value" => $this->value,
            "class" => $this->getClassStr()
        ];
        if (!empty($options))
            $data = ArrayHelper::merge($data, $options);
        if (!empty($this->inputId))
            $data['id'] = $this->inputId;
        if (!empty($this->verify))
            $data['layer-verify'] = $this->verify;
        if (!empty($this->filter))
            $data['layer-filter'] = $this->filter;
        if (!empty($this->placeholder))
            $data['placeholder'] = $this->placeholder;
        return '<input' . self::generateOptions($data) . '/>';
    }
}