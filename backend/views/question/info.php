<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/18
 * Time: 下午9:51
 */

use layuiAdm\tools\Url;
use common\models\Question;
use common\models\QuestionType;
use layuiAdm\widgets\FormWidget;
use layuiAdm\widgets\FormInputWidget;
use layuiAdm\widgets\SelectWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;

/* @var $question Question */

FormWidget::begin([
    "formType" => FormWidget::FORM_COLUMN
]);

echo FormInputWidget::widget([
    "formType" => FormWidget::FORM_COLUMN,
    "type"     => "textarea",
    "label"    => "题干",
    "name"     => "title",
    "value"    => $question->title
]);

FormWidget::end([
    "formType" => FormWidget::FORM_COLUMN
]);