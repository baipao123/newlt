<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/24
 * Time: 下午11:36
 */
use layuiAdm\tools\Url;
use common\models\UserExam;
use common\models\QuestionType;
use layuiAdm\widgets\FormWidget;
use layuiAdm\widgets\FormItemWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;
use yii\helpers\ArrayHelper;

/* @var $pagination \yii\data\Pagination */

FormWidget::begin([

]);
echo FormItemWidget::widget([
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
    "type"    => "number",
    "options" => [
        "name"        => "uid",
        "value"       => $uid ?: null,
        "placeholder" => "用户ID",
    ]
]);

FormWidget::end();

TableWidget::begin([
    "header"       => [
        "模考ID"   => ["fixed" => "left", "width" => 80, "unresize" => true],
        "科目"     => ["minWidth" => 140],
        "微信昵称",
        "总分",
        "得分",
        "总题量",
        "正确数量",
        "错误数量",
        "耗时",
        "开始时间",
        "平均分"  => ["fixed" => "right", "width" => 80, "unresize" => true],
        "模考次数" => ["fixed" => "right", "width" => 80, "unresize" => true],
        "最高分"  => ["fixed" => "right", "width" => 80, "unresize" => true]
    ],
    "height"       => 500,
    "cellMinWidth" => 60,
    "limit"        => $pagination->pageSize,
]);

/* @var $list \common\models\UserExam[] */
foreach ($list as $exam) {
    ?>
    <tr>
        <td><?= $exam->id ?></td>
        <td><?= $exam->type->name ?></td>
        <td><?= $exam->user->nickname ?></td>
        <?php
        $info = json_decode($exam->detail, true);
        $typeInfo = $exam->type->setting();
        $examInfo = UserExam::examInfo($exam->uid, $exam->tid);
        ?>
        <td><?= ArrayHelper::getValue($typeInfo, "totalScore") ?></td>
        <td><?= $exam->score ?></td>
        <td><?= ArrayHelper::getValue($info, "total") ?></td>
        <td><?= ArrayHelper::getValue($info, "passNum") ?></td>
        <td><?= ArrayHelper::getValue($info, "failNum") ?></td>
        <td><?= $exam->useTime() ?></td>
        <td><?= date("Y-m-d H:i:s", $exam->created_at) ?></td>
        <td><?= number_format(ArrayHelper::getValue($examInfo, "avg"), 1) ?></td>
        <td><?= ArrayHelper::getValue($examInfo, "num") ?></td>
        <td><?= ArrayHelper::getValue($examInfo, "max") ?></td>
    </tr>

    <?php
}
TableWidget::end();
echo PagesWidget::widget([
    "pagination" => $pagination
])
?>

