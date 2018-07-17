<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/17
 * Time: 下午8:42
 */

namespace layuiAdm\widgets;


class SelectWidget extends Widget
{
    public $title = "";

    public $name = "";

    public $options = [];

    public $group = false;

    public $value = null;

    public $search = false;

    public $valueKey = "";

    public $textKey = "";

    public $placeHolder;

    public $placeHolderValue = "";

    public $filter = "";

    public $verify = "";

    public function run() {
        $html = $this->start();
        $html .= $this->select();
        if (!empty($this->placeHolder))
            $html .= $this->option($this->placeHolderValue, $this->placeHolder);
        if ($this->group) {
            foreach ($this->options as $key => $val) {
                $html .= '<optgroup label="' . $key . '">';
                $html .= $this->options($val);
                $html .= '</optgroup>';
            }
        } else
            $html .= $this->options($this->options);
        $html .= '</select>';
        $html .= $this->finish();
        return $html;
    }

    protected function start() {
        $html = '<div class="layui-inline">' . "\n";
        if (!empty($this->title))
            $html .= '<label class="layui-form-label">' . $this->title . '</label>' . "\n";
        $html .= '<div class="layui-input-inline">' . "\n";
        return $html;
    }

    protected function finish() {
        return "</div>\n</div>\n";
    }

    protected function select() {
        $str = '<select ';
        if (!empty($this->name))
            $str .= 'name="' . $this->name . '" ';
        if (!empty($this->filter))
            $str .= 'lay-verify="' . $this->verify . '" ';
        if (!empty($this->filter))
            $str .= 'lay-filter="' . $this->filter . '" ';
        if ($this->search)
            $str .= 'lay-search="" ';
        return trim($str) . '>';
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