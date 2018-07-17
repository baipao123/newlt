<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/17
 * Time: 下午8:40
 */
use layuiAdm\tools\Url;
use common\models\Question;
use common\models\QuestionType;
use layuiAdm\widgets\FormWidget;

echo FormWidget::begin([

]);
echo \layuiAdm\widgets\SelectWidget::widget([
    "title"       => "所属科目",
    "name"        => "tid",
    "options"     => QuestionType::typesForSelect(),
    "group"       => true,
    "value"       => $tid,
    "valueKey"    => "tid",
    "textKey"     => "name",
    "placeHolder" => "请选择科目",
    "search"      => true
]);

echo \layuiAdm\widgets\SelectWidget::widget([
    "title"       => "题目类型",
    "name"        => "type",
    "options"     => [Question::TypeJudge=>"判断题",Question::TypeSelect=>"单选题",Question::TypeMulti=>"多选题"],
    "value"       => $type,
    "placeHolder" => "全部题型",
]);

echo FormWidget::end();
?>

