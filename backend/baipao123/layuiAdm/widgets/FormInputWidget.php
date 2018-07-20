<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/12
 * Time: 上午12:19
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class FormInputWidget extends FormBaseWidget
{

    public function run() {
        if ($this->formType == self::FORM_ROW)
            return $this->rowRun();

        $html = $this->itemHead();

        $config = $this->inputConfig();

        if ($this->type == 'radio')
            $html .= "";
        else if ($this->type == 'checkbox')
            $html .= "";
        else if ($this->type == 'textarea')
            $html .= TextAreaWidget::widget($config);
        else
            $html .= InputWidget::widget($config);

        return $html . $this->itemEnd();
    }

    public function rowRun() {

    }

}