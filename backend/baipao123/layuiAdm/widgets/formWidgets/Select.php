<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午8:05
 */

namespace layuiAdm\widgets\formWidgets;


class Select extends InputOptions
{
    public $defaultClasses = ['layui-textarea'];
    public $needValue = false;

    public $options = [];

    public $group = false;

    public $valueKey = "";

    public $textKey = "";

    public $placeHolderValue = "";

    public function run() {
        $config = $this->config();
        if (isset($config['placeholder']))
            unset($config['placeholder']);

        $html = '<select ' . self::generateOptions($config) . '>';
        if (!empty($this->placeholder))
            $html .= $this->option($this->placeHolderValue, $this->placeholder);
        if ($this->group) {
            foreach ($this->options as $key => $val) {
                $html .= '<optgroup label="' . $key . '">';
                $html .= $this->options($val);
                $html .= '</optgroup>';
            }
        } else
            $html .= $this->options($this->options);
        $html .= '</select>';
        return $html;
    }

    protected function options($data) {
        $html = "";
        foreach ($data as $key => $val) {
            if (is_string($val) || is_int($val))
                $html .= $this->option($key, $val);
            else
                $html .= $this->option($val[ $this->valueKey ], $val[ $this->textKey ]);
        }
        return $html;
    }

    protected function option($value = "", $text = "") {
        return '<option value="' . $value . '" ' . ($value == $this->value ? 'selected' : '') . '>' . $text . '</option>';
    }

}