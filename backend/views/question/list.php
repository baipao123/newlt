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
use layuiAdm\widgets\SelectWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;

FormWidget::begin([

]);
echo SelectWidget::widget([
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

echo SelectWidget::widget([
    "title"       => "题目类型",
    "name"        => "type",
    "options"     => [Question::TypeJudge => "判断题", Question::TypeSelect => "单选题", Question::TypeMulti => "多选题"],
    "value"       => $type,
    "placeHolder" => "全部题型",
]);

FormWidget::end();

TableWidget::begin([
    "header" => [
        "题目ID" => ["fixed" => "left", "width" => 80, "unresize" => true],
        "所属科目", "题型", "标题", "选项", "答案", "解析", "知识点", "难度系数",
        "操作"   => ["fixed" => "right", "width" => 280, "unresize" => true]
    ],
    "cellMinWidth" => 60,
    "limit" => 20,
]);

/* @var $list Question[] */
foreach ($list as $question) {
    ?>
    <tr>
        <td><?= $question->id ?></td>
        <td><?= $question->tid ?></td>
        <td><?= $question->type ?></td>
        <td>
            <?= $question->title ?>
            <?php foreach ($question->attaches() as $attach): ?>
                <img src="<?= Img::format($attach,100,0,true) ?>">
            <?php endforeach; ?>
        </td>
        <td><?php ?></td>
        <td><?= $question->answer ?></td>
        <td><?= $question->description ?></td>
        <td><?= $question->knowledge ?></td>
        <td><?= $question->difficulty ?></td>
        <td></td>
    </tr>

    <?php
}
TableWidget::end();
echo PagesWidget::widget([
    "pagination" => $pagination
])
?>

<script>
    $("td>img").click(function (e) {
        let classTxt = $(this).parent().eq(0).attr("class");
        if (!classTxt)
            return true;
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>

