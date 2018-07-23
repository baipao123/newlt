<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/23
 * Time: 下午7:28
 */

use layuiAdm\tools\Url;
use common\models\Order;
use common\models\QuestionType;
use layuiAdm\widgets\FormWidget;
use layuiAdm\widgets\FormItemWidget;
use layuiAdm\widgets\TableWidget;
use layuiAdm\widgets\PagesWidget;
use common\tools\Img;
use common\tools\Status;

/* @var $pagination \yii\data\Pagination */

FormWidget::begin([

]);
echo FormItemWidget::widget([
    "label"   => "科目",
    "type"    => "select",
    "options" => [
        "name"        => "tid",
        "options"     => QuestionType::typesForSelect(false),
        "group"       => true,
        "value"       => $tid,
        "valueKey"    => "tid",
        "textKey"     => "name",
        "placeholder" => "请选择科目",
        "search"      => true
    ]
]);

echo FormItemWidget::widget([
    "label"   => "用户ID",
    "options" => [
        "name"  => "uid",
        "value" => $uid,
    ]
]);

echo FormItemWidget::widget([
    "label"   => "价格ID",
    "options" => [
        "name"  => "pid",
        "value" => $pid,
    ]
]);

echo FormItemWidget::widget([
    //    "label"   => "订单状态",
    "type"    => "select",
    "options" => [
        "name"        => "status",
        "options"     => Status::order(),
        "group"       => true,
        "value"       => $status,
        "valueKey"    => "tid",
        "textKey"     => "name",
        "placeholder" => "请选择订单状态",
        "search"      => true
    ]
]);

FormWidget::end();


TableWidget::begin([
    "header"       => [
        "订单ID" => ["fixed" => "left", "width" => 80, "unresize" => true],
        "科目"   => ['minWidth' => 110],
        "微信昵称" => ['width' => 200],
        "时长",
        "价格",
        "状态",
        "付款时间" => ["minWidth" => 200],
        "下单时间" => ["minWidth" => 200],
        "操作"   => ["fixed" => "right", "width" => 150, "unresize" => true]
    ],
    "height"       => 500,
    "cellMinWidth" => 60,
    "limit"        => $pagination->pageSize,
]);

/* @var $list Order[] */
foreach ($list as $order) {
    ?>
    <tr>
        <td><?= $order->id ?></td>
        <td><?= $order->qType->name ?></td>
        <td><?= $order->user->nickname ?></td>
        <td><?= $order->hour ?>小时</td>
        <td><?= $order->price / 100 ?>元</td>
        <td>
            <?php if (in_array($order->status, [Status::WAIT_PAY, Status::WAIT_NOTIFY, Status::IS_UNIFY_ORDER])): ?>
                <span class="layui-btn layui-btn-xs layui-btn-normal"><?= Status::order($order->status) ?></span>
            <?php elseif ($order->status == Status::CANCEL_PAY): ?>
                <span class="layui-btn layui-btn-xs layui-btn-primary"><?= Status::order($order->status) ?></span>
            <?php elseif ($order->status == Status::IS_PAY): ?>
                <span class="layui-btn layui-btn-xs"><?= Status::order($order->status) ?></span>
            <?php elseif ($order->status == Status::IS_REFUND): ?>
                <span class="layui-btn layui-btn-xs layui-btn-danger"><?= Status::order($order->status) ?></span>
            <?php else: ?>
                <span class="layui-btn layui-btn-xs layui-btn-warm"><?= Status::order($order->status) ?></span>
            <?php endif; ?>
        </td>
        <td><?= $order->paytime ?></td>
        <td><?= date("Y-m-d H:i:s", $order->created_at) ?></td>
        <td>
            <a class="layui-btn layui-btn-xs layui-btn-normal"
               onclick="layerConfirmUrl('<?= Url::createLink("order/info", ["oid" => $order->id]) ?>')">订单详情</a>
            <?php if ($order->status == Status::IS_PAY): ?>
                <a class="layui-btn layui-btn-xs layui-btn-warm"
                   onclick="refund(<?= $order->id ?>,<?= $order->price / 100 ?>)">退款</a>
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

<script>
    function refund(oid, price) {
        globalLayer.confirm("请选择退款类型", {
            btn: ["退全款", "部分退款"]
        }, function () {
            layerConfirmUrl("/order/refund?oid=" + oid, "确定吗?");
        }, function () {
            globalLayer.prompt({
                title: "请输入退款金额（元）"
            }, function (p, index) {
                if (!p.match(/(^\d{1,10}$)|(^\d{1,10}\.\d{1,2}$)/) || p > price || p <= 0)
                    globalLayer.msg("退款金额必须是2位小数,且为小于" + price + "的正数");
                else {
                    globalLayer.close(index);
                    layerConfirmUrl("/order/refund?oid=" + oid + "&price=" + p, "确定退款" + p + "元吗?");
                }
            })
        })
    }
</script>
