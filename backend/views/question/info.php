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
    "tips"    => "科目提交后无法修改，如欲修改，请删除后重新添加",
    "options" => [
        "name"        => "tid",
        "options"     => QuestionType::typesForSelect(),
        "group"       => true,
        "value"       => $question->tid,
        "valueKey"    => "tid",
        "textKey"     => "name",
        "placeholder" => "请选择科目",
        "search"      => true,
        "disabled"    => !$question->isNewRecord
    ]
]);

echo FormItemWidget::widget([
    "label"   => "题目类型",
    "type"    => "radio",
    "tips"    => "题型提交后无法修改，如欲修改，请删除后重新添加",
    "options" => [
        "name"    => "type",
        "options" => $question->typesForAdm(),
        "value"   => $question->type,
        "filter"  => "type"
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

if ($question->type != Question::TypeJudge || $question->isNewRecord) {

    echo FormItemWidget::widget([
        "type"    => "text",
        "label"   => "选项A",
        "classes" => "noJudge",
        "options" => [
            "name"  => "a",
            "value" => $question->a
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项A配图",
        "classes" => "noJudge",
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
        "classes" => "noJudge",
        "options" => [
            "name"  => "b",
            "value" => $question->b
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项B配图",
        "classes" => "noJudge",
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
        "classes" => "noJudge",
        "options" => [
            "name"  => "c",
            "value" => $question->c
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项C配图",
        "classes" => "noJudge",
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
        "classes" => "noJudge",
        "options" => [
            "name"  => "d",
            "value" => $question->d
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项D配图",
        "classes" => "noJudge",
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
        "classes" => "noJudge",
        "options" => [
            "name"  => "e",
            "value" => $question->e
        ]
    ]);

    echo FormItemWidget::widget([
        "type"    => "img",
        "label"   => "选项E配图",
        "classes" => "noJudge",
        "options" => [
            "isMulti" => true,
            "hint"    => "推荐尺寸:200*200",
            "name"    => 'eImg',
            "value"   => $question->eImg
        ]
    ]);
}

echo FormItemWidget::widget([
    "type"    => "checkbox",
    "label"   => "答案",
    "options" => [
        "name"    => "answer[]",
        "value"   => str_split($question->answer),
        "options" => [
            "A", "B",
            [
                "value" => "C",
                "class" => "noJudge"
            ],
            [
                "value" => "D",
                "class" => "noJudge"
            ],
            [
                "value" => "E",
                "class" => "noJudge"
            ]
        ],
    ]
]);

echo FormItemWidget::widget([
    "type"    => "textarea",
    "label"   => "答案解析",
    "options" => [
        "name"  => "description",
        "value" => $question->description,
    ]
]);

echo FormItemWidget::widget([
    "type"    => "textarea",
    "label"   => "知识点",
    "options" => [
        "name"  => "knowledge",
        "value" => $question->knowledge,
    ]
]);

echo FormItemWidget::widget([
    "type"    => "number",
    "label"   => "难度系数",
    "options" => [
        "name"  => "difficulty",
        "value" => $question->difficulty,
    ]
]);

echo FormItemWidget::widget([
    "type"    => "switch",
    "label"   => "是否开启",
    "options" => [
        "name"      => "status",
        "value"     => $question->status == Status::PASS || $question->isNewRecord,
        "falseText" => "关闭",
        "trueText"  => "开启"
    ]
]);

FormWidget::end();

?>
<script>
    layui.use("form", function () {
        var form = layui.form;
        form.on("radio(type)", function (data) {
            console.log(data)
            var value = data.value,
                isJudge = value == <?=Question::TypeJudge?>

                    console.log(isJudge)
            if (isJudge)
                $(".noJudge").hide()
            else
                $(".noJudge").show()
            form.render()
        })
    })
</script>
