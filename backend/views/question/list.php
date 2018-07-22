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
use layuiAdm\widgets\FormItemWidget;
use layuiAdm\widgets\SelectWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;

/* @var $pagination \yii\data\Pagination */

FormWidget::begin([

]);
echo FormItemWidget::widget([
    "label"   => "所属科目",
    "type"    => "select",
    "options" => [
        "name"        => "tid",
        "options"     => QuestionType::typesForSelect(),
        "group"       => true,
        "value"       => $tid,
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
        "value"       => $type,
        "placeholder" => "全部题型",
    ]
]);

FormWidget::end();
?>
<button class="layui-btn layui-btn-danger"
        onclick="layerOpenIFrame('<?= Url::createLink('/question/info', ["id" => 0]) ?>','添加题目',['100%','100%'])"><i class="layui-icon">&#xe654;</i>添加题目
</button>

<?php

TableWidget::begin([
    "header"       => [
        "题目ID" => ["fixed" => "left", "width" => 80, "unresize" => true],
        "所属科目" => ['minWidth' => 110],
        "题型"   => ['width' => 75],
        "标题"   => ["minWidth" => 400],
        "选项"   => ["minWidth" => 400],
        "答案",
        "解析"   => ["minWidth" => 100],
        "知识点"  => ["minWidth" => 100], "难度系数",
        "浏览量"  => ["fixed" => "right"],
        "正确量"  => ["fixed" => "right"],
        "错误量"  => ["fixed" => "right"],
        "操作"   => ["fixed" => "right", "width" => 150, "unresize" => true]
    ],
    "height"       => 500,
    "cellMinWidth" => 60,
    "limit"        => $pagination->pageSize,
]);

/* @var $list Question[] */
foreach ($list as $question) {
    ?>
    <tr>
        <td><?= $question->id ?></td>
        <td><?= $question->questionType->name ?></td>
        <td><?= $question->questionType->typeCNStr($question->type) ?></td>
        <td><?php
            echo $question->title;
            foreach ($question->attaches() as $attach): ?>
                <img src="<?= Img::format($attach, 0, 0, true) ?>">
            <?php endforeach; ?>
        </td>
        <td><?php
            foreach ($question->options() as $o) {
                echo $o['option'] . ".";
                echo $o['text'];
                if (!empty($o['img']))
                    echo "<img src='" . Img::format($o['img'], 0, 0, true) . "'>";
                echo "&emsp;&emsp;";
            } ?></td>
        <td><?= $question->answer ?></td>
        <td><?= $question->description ?></td>
        <td><?= $question->knowledge ?></td>
        <td><?= $question->difficulty ?></td>
        <td><?= $question->view_num ?></td>
        <td><?= $question->success_num ?></td>
        <td><?= $question->fail_num ?></td>
        <td>
            <span class="layui-btn layui-btn-xs layui-btn-normal"
                  onclick="layerOpenIFrame('<?= Url::createLink('question/info', ['qid' => $question->id]) ?>','编辑题目',['100%','100%'])">编辑</span>
            <?php if ($question->status == Status::FORBID): ?>
                <span class="layui-btn layui-btn-xs"
                      onclick="layerConfirmUrl('<?= Url::createLink("question/toggle", ["qid" => $question->id, "status" => Status::PASS]) ?>')">开启</span>
                <span class="layui-btn layui-btn-xs layui-btn-primary"
                      onclick="layerConfirmUrl('<?= Url::createLink("question/toggle", ["qid" => $question->id, "status" => Status::DELETE]) ?>','确定删除？删除后无法恢复')">删除</span>
            <?php elseif ($question->status == Status::PASS): ?>
                <span class="layui-btn layui-btn-xs layui-btn-warm"
                      onclick="layerConfirmUrl('<?= Url::createLink("question/toggle", ["qid" => $question->id, "status" => Status::FORBID]) ?>')">关闭</span>
            <?php endif; ?>
        </td>
    </tr>

    <?php
}
TableWidget::end();
echo PagesWidget::widget([
    "pagination" => $pagination
])
?>

