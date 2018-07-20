<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午6:37
 */

namespace layuiAdm\widgets;


use layuiAdm\widgets\formWidgets\Input;
use layuiAdm\widgets\formWidgets\Select;
use layuiAdm\widgets\formWidgets\TextArea;
use yii\helpers\ArrayHelper;

class FormItemWidget extends Widget
{
    public $type = "text";

    public $label;

    public $tips;

    public $options;

    public function run() {
        return $this->itemHead() . $this->component() . $this->itemEnd();
    }

    public function component() {
        switch ($this->type) {
            case "radio":
                return "";
            case "checkbox":
                return "";
            case "switch":
                return "";
            case "textarea":
                return TextArea::widget($this->options);
            case "select":
                return Select::widget($this->options);
            default:
                return Input::widget($this->options);
        }
    }

    public function itemHead() {
        $html = '<div class="layui-' . (self::$form_default == self::FORM_ROW ? "inline" : "form-item") . '">' . "\n";
        if (!empty($this->label))
            $html .= '<label class="layui-form-label">' . $this->label . '</label>' . "\n";
        $html .= '<div class="layui-input-' . (self::$form_default == self::FORM_ROW ? 'inline' : 'block') . '">' . "\n";
        return $html;
    }

    public function itemEnd() {
        $html = '';
        if (!empty($this->tips))
            $html .= '<div class="layui-form-mid layui-word-aux">' . $this->tips . '</div>' . "\n";
        $html .= '</div></div>' . "\n";
        return $html;
    }

}