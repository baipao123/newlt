<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/22
 * Time: 下午7:54
 */

namespace layuiAdm\widgets\formWidgets;


use yii\helpers\ArrayHelper;

class RadioList extends InputOptions
{


    public $defaultClasses = [];
    public $needValue = false;

    public $type = "radio";

    /**
     * array
     * value
     * title
     * disabled
     */
    public $options;

    public $skin;

    public function run() {
        $commonConfig = $this->config();
        $html = '';
        foreach ($this->options as $val) {
            $config = $commonConfig;
            if (is_string($val) || is_int($val)) {
                $config['value'] = $val;
                $config['title'] = $val;
            } else {
                $config['value'] = ArrayHelper::getValue($val, "value", "");
                $config['title'] = ArrayHelper::getValue($val, "title", $config['value']);
                if (ArrayHelper::getValue($val, "disabled", false)) {
                    $config['disabled'] = "";
                }
                $class = ArrayHelper::getValue($val, "class", "");
                if (!empty($class))
                    $config['class'] .= " " . self::optionsToStr($class);
            }
            if ($config['value'] == $this->value)
                $config['checked'] = "";
            if (!empty($this->skin))
                $config['lay-skin'] = $this->skin;
            $html .= '<input' . self::generateOptions($config) . '/>';
        }
        return $html;
    }

}