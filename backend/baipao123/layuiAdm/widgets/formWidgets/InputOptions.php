<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午7:26
 */

namespace layuiAdm\widgets\formWidgets;

use layuiAdm\widgets\Widget;
use yii\helpers\ArrayHelper;

class InputOptions extends Widget
{
    public $type = "text";
    public $defaultClasses;

    public $ids;
    public $classes;

    public $name;
    public $value;
    public $placeholder;

    public $needValue = true;

    public $extOptions;

    public $search = false;
    public $autoComplete = false;
    public $disabled = false;
    public $verify;
    public $filter;

    public function config() {
        $this->ids[] = $this->id;
        $config = [
            "id"          => self::optionsToStr($this->ids),
            "class"       => self::optionsToStr($this->defaultClasses, $this->classes),
            "type"        => $this->type,
            "name"        => $this->name,
        ];
        if (!empty($this->placeholder))
            $config['placeholder'] = $this->placeholder;
        if ($this->needValue && !is_null($this->value))
            $config['value'] = $this->value;
        if ($this->autoComplete)
            $config['autocomplete'] = 'on';
        if ($this->disabled)
            $config['disabled'] = '';
        if (!empty($this->verify))
            $config['lay-verify'] = $this->verify;
        if (!empty($this->filter))
            $config['lay-filter'] = $this->filter;
        if ($this->search)
            $config['lay-search'] = '';
        if (!empty($this->extOptions))
            $config = ArrayHelper::merge($config, $this->extOptions);
        return $config;
    }
}