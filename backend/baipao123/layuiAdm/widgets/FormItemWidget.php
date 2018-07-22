<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午6:37
 */

namespace layuiAdm\widgets;


use layuiAdm\widgets\formWidgets\CheckBox;
use layuiAdm\widgets\formWidgets\ImgList;
use layuiAdm\widgets\formWidgets\Input;
use layuiAdm\widgets\formWidgets\RadioList;
use layuiAdm\widgets\formWidgets\Select;
use layuiAdm\widgets\formWidgets\SwitchBar;
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
                return RadioList::widget($this->options);
            case "checkbox":
                return CheckBox::widget($this->options);
            case "switch":
                return SwitchBar::widget($this->options);
            case "img":
                return ImgList::widget($this->options);
            case "textarea":
                return TextArea::widget($this->options);
            case "select":
                return Select::widget($this->options);
            default:
                return Input::widget($this->options);
        }
    }

    public function itemHead() {
        $this->defaultClasses = self::$form_default == self::FORM_ROW ? "layui-inline" : "layui-form-item";
        $html = '<div class="' . $this->getClassStr() . '">' . "\n";
        if (!empty($this->label))
            $html .= '<label class="layui-form-label">' . $this->label . '</label>' . "\n";
        $html .= '<div class="layui-input-' . (self::$form_default == self::FORM_ROW ? 'inline' : 'block') . '">' . "\n";
        return $html;
    }

    public function itemEnd() {
        $html = '</div>' . "\n";
        if (!empty($this->tips))
            $html .= '<div class="layui-form-mid layui-word-aux">' . $this->tips . '</div>' . "\n";
        $html .= '</div>' . "\n";
        return $html;
    }

}