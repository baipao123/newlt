<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午6:37
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class FormBaseWidget extends Widget
{
    public $defaultClasses = "layui-form-item";

    public $label;

    public $tips;

    public $type = "text";
    public $name;
    public $value = "";

    public $inputClasses;
    public $placeholder = "";

    public $autocomplete = false;
    public $disabled = false;

    public $options = [];

    public $filter;
    public $verify;

    public function itemHead() {
        $html = '<div class="' . $this->getClassStr() . '">' . "\n";
        if (!empty($this->label))
            $html .= '<label class="layui-form-label">' . $this->label . '</label>' . "\n";
        $html .= '<div class="layui-input-' . (self::$form_default == self::FORM_ROW ? 'block' : 'inline') . '">' . "\n";
        return $html;
    }

    public function itemEnd() {
        $html = '';
        if (!empty($this->tips))
            $html .= '<div class="layui-form-mid layui-word-aux">' . $this->tips . '</div>' . "\n";
        $html .= '</div></div>' . "\n";
        return $html;
    }

    public function inputConfig($options = []) {
        $options = ArrayHelper::merge($this->options, [
            "autocomplete" => $this->autocomplete ? "on" : "off"
        ], $options);
        $config = [
            "classes"     => $this->inputClasses,
            "type"        => $this->type,
            "name"        => $this->name,
            "value"       => $this->value,
            "placeholder" => $this->placeholder,
            "options"     => $options,
            "filter"      => $this->filter,
            "verify"      => $this->verify
        ];
        if ($this->disabled)
            $config['disabled'] = '';
        return $config;
    }


    protected function getClassStr() {
        $class = self::$form_default == self::FORM_ROW ? "layui-form-inline " : "layui-form-item ";
        return $class . parent::getClassStr();
    }


}