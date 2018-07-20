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
use layuiAdm\widgets\FormItemWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;

/* @var $question Question */

Widget::setDefaultFormType(Widget::FORM_COLUMN);

FormWidget::begin();

echo FormItemWidget::widget([
    "label"   => "所属科目",
    "type"    => "select",
    "options" => [
        "name"        => "tid",
        "options"     => QuestionType::typesForSelect(),
        "group"       => true,
        "value"       => $question->tid,
        "valueKey"    => "tid",
        "textKey"     => "name",
        "placeholder" => "请选择科目",
        "search"      => true
    ]
]);

echo FormItemWidget::widget([
    "label"   => "题目类型",
    "type"    => "select",
    "options" => [
        "name"        => "type",
        "options"     => Question::TypeAll,
        "value"       => $question->type,
        "placeholder" => "全部题型",
    ]
]);


echo FormItemWidget::widget([
    "type"    => "textarea",
    "label"   => "题干",
    "options" => [
        "name"  => "title",
        "value" => $question->title
    ]
]);

echo FormItemWidget::widget([
    "type"    => "img",
    "label"   => "题干配图",
    "options" => [
        "isMulti" => true,
        "hint"    => "推荐尺寸:200*200",
        "name"    => 'attaches',
        "value"   => $question->attaches
    ]
]);

if($question->type != Question::TypeJudge) {

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项A",
        "options" => [
            "name"  => "a",
            "value" => $question->a
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项A配图",
        "options" => [
            "isMulti" => false,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'aImg',
            "value"   => $question->aImg
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项B",
        "options" => [
            "name"  => "b",
            "value" => $question->b
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项B配图",
        "options" => [
            "isMulti" => false,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'bImg',
            "value"   => $question->bImg
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项C",
        "options" => [
            "name"  => "c",
            "value" => $question->c
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项C配图",
        "options" => [
            "isMulti" => false,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'cImg',
            "value"   => $question->cImg
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项D",
        "options" => [
            "name"  => "d",
            "value" => $question->d
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项D配图",
        "options" => [
            "isMulti" => false,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'dImg',
            "value"   => $question->dImg
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项E",
        "options" => [
            "name"  => "e",
            "value" => $question->e
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项E配图",
        "options" => [
            "isMulti" => true,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'eImg',
            "value"   => $question->eImg
        ]
    ]);
}



if (in_array($question->type, [Question::TypeSelect, Question::TypeMulti])) {

}

FormWidget::end();