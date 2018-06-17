<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/11
 * Time: 下午11:48
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class FormWidget extends Widget
{
    public $className;

    public $title;

    public $method;

    public static function begin($config = []) {
        $className = ArrayHelper::getValue($config, "className");
        $title = ArrayHelper::getValue($config, "title", "检索");
        $method = ArrayHelper::getValue($config, "method", "get");
        $html = '<fieldset class="layui-elem-field ' . $className . '">';
        $html .= '<legend>' . $title . '</legend>';
        $html .= '<div class="layui-field-box">';
        $html .= '<form class="layui-form" method="' . $method . '">';
        return $html;
    }

    public static function end() {
        return "</form></div></fieldset>";
    }

}