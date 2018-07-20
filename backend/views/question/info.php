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
use layuiAdm\widgets\Widget;
use layuiAdm\widgets\FormWidget;
use layuiAdm\widgets\FormInputWidget;
use layuiAdm\widgets\SelectWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;

/* @var $question Question */

Widget::setDefaultFormType(Widget::FORM_COLUMN);

FormWidget::begin();

echo SelectWidget::widget([
    "title"       => "所属科目",
    "name"        => "tid",
    "options"     => QuestionType::typesForSelect(),
    "group"       => true,
    "value"       => $question->tid,
    "valueKey"    => "tid",
    "textKey"     => "name",
    "placeHolder" => "请选择科目",
    "search"      => true
]);

echo SelectWidget::widget([
    "title"       => "题目类型",
    "name"        => "type",
    "options"     => Question::TypeAll,
    "value"       => $question->type,
    "placeHolder" => "全部题型",
]);


echo FormInputWidget::widget([
    "type"     => "textarea",
    "label"    => "题干",
    "name"     => "title",
    "value"    => $question->title
]);

// 题干配图

if (in_array($question->type, [Question::TypeSelect, Question::TypeMulti])) {

}

FormWidget::end();