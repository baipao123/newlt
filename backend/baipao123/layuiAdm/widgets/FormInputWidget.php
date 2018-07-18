<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/12
 * Time: 上午12:19
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class FormInputWidget extends Widget
{
    public $defaultClasses = "layui-form-item";
    public $classes;

    public $inputClasses;

    public $label;

    public $type = "text";
    public $name;
    public $value = "";

    public $placeholder = "";
    public $autocomplete = false;

    public $options = [];

    public $tips;

    public $filter;
    public $verify;


    public function run() {
        if ($this->formType == self::FORM_ROW)
            return $this->rowRun();

        $html = '<div class="' . $this->getClassStr() . '">';
        if (!empty($this->label))
            $html .= '<label class="layui-form-label">' . $this->label . '</label>';
        $html .= '<div class="layui-input-block">';

        $options = ArrayHelper::merge($this->options, [
            "autocomplete" => $this->autocomplete ? "on" : "off"
        ]);
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

        if ($this->type == 'radio')
            $html .= "";
        else if ($this->type == 'checkbox')
            $html .= "";
        else if ($this->type == 'textarea')
            $html .= TextAreaWidget::widget($config);
        else
            $html .= InputWidget::widget($config);


        if (!empty($this->tips))
            $html .= '<div class="layui-form-mid layui-word-aux">' . $this->tips . '</div>';
        $html .= '</div></div>';
        return $html;
    }

    public function rowRun() {

    }

}