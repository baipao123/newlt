<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/22
 * Time: 下午7:57
 */

namespace layuiAdm\widgets\formWidgets;


class SwitchBar extends InputOptions
{
    public $defaultClasses = [];
    public $needValue = false;

    public $type = "checkbox";

    public $falseText = "ON";
    public $trueText = "OFF";

    public $skin = "switch";

    public function run() {
        $config = $this->config();
        $config['lay-skin'] = $this->skin;
        $config['lay-text'] = $this->trueText . '|' . $this->falseText;
        if ($this->value)
            $config['checked'] = "";
        return '<input ' . self::generateOptions($config) . ' />';
    }

}