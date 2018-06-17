<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/12
 * Time: 上午12:19
 */

namespace layuiAdm\widgets;


class FormInputWidget extends Widget
{
    public $className;

    public $label;

    public $name;

    public $type = "text";

    public $value = "";

    public $placeHolder = "";

    public $auxText;

    public $filter;

    public $verify;

    public $formStyle = "inline";

    public $autoComplete = false;

    public function run(){
        $html = '<div class="layui-inline">';
        $html .= '<label class="layui-form-label">' . $this->label . '</label>';
        $html .= '<div class="layui-input-inline">';
        $filter = empty($this->filter) ? '' : 'lay-filter="' . $this->filter . '"';
        $verify = empty($this->verify) ? '' : 'lay-verify="' . $this->verify . '"';
        $html .= '<input type="text" name="' . $this->name . '" autocomplete="' . $this->autoComplete ? 'on' : 'off' . '" ' . $filter . ' ' . $verify . ' class="layui-input ' . $this->className . '" value="' . $this->value . '" placeholder="' . $this->placeHolder . '">';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}