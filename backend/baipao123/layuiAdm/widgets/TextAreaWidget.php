<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/18
 * Time: 下午11:11
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class TextAreaWidget extends Widget
{
    public $type; // 无用

    public $name;
    public $value;

    public $placeholder;

    public $options;


    public $defaultClasses = ['layui-textarea'];
    public $classes;

    public $inputId;

    public $verify;
    public $filter;

    public function run() {
        $data = [
            "name" => $this->name,
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
        return '<textarea' . self::generateOptions($data) . '>' . $this->value . '</textarea>';
    }
}