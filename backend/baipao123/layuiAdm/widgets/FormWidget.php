<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/11
 * Time: 下午11:48
 */

namespace layuiAdm\widgets;

use layuiAdm\widgets\formWidgets\Input;
use yii;
use yii\helpers\ArrayHelper;

class FormWidget extends Widget
{
    public $className;

    public $title;

    public $method;

    public static function begin($config = []) {
        $formType = ArrayHelper::getValue($config, "formType", self::$form_default);
        if ($formType == self::FORM_ROW) {
            self::rowBegin($config);
            return;
        }

        $classes = ArrayHelper::getValue($config, "classes", "");
        $method = ArrayHelper::getValue($config, "method", "post");
        $action = ArrayHelper::getValue($config, "action", "");
        $data = [
            "class"  => self::generateClassStr("layui-form", $classes),
            "method" => $method,
        ];
        if (!empty($action))
            $data['action'] = $action;
        $html = '<form' . self::generateOptions($data) . '>';
        $html .= Input::widget([
            "type"  => "hidden",
            "name"  => Yii::$app->request->csrfParam,
            "value" => Yii::$app->request->getCsrfToken()
        ]);
        echo $html;
        return;
    }

    public static function end($config = []) {
        $formType = ArrayHelper::getValue($config, "formType", self::$form_default);
        if ($formType == self::FORM_ROW) {
            self::rowEnd();
            return;
        }

        $button = ArrayHelper::getValue($config, "button", "立即提交");

        $html = '<div class="layui-form-item">';
        $html .= '<div class="layui-input-block">';
        $html .= '<button class="layui-btn" lay-submit lay-filter="submit">' . $button . '</button>';
        $html .= '</div></div></form>';
        echo $html;
        return;

    }

    /**
     * @param array $config
     * @return void
     **/
    public static function rowBegin($config = []) {
        $className = ArrayHelper::getValue($config, "className", "");
        $title = ArrayHelper::getValue($config, "title", "检索");
        $method = ArrayHelper::getValue($config, "method", "get");
        $action = ArrayHelper::getValue($config, "action", "");
        $html = '<fieldset class="layui-elem-field ' . $className . '">';
        $html .= '<legend>' . $title . '</legend>';
        $html .= '<div class="layui-field-box">';
        $html .= '<form class="layui-form" method="' . $method . '" action="' . $action . '">';
        echo $html;
    }

    /**
     * @return void
     **/
    public static function rowEnd() {
        $html = '<div class="layui-inline"><button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button></div>';
        $html .= "</form></div></fieldset>";
        echo $html;
    }

}