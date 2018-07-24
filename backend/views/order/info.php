<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/24
 * Time: 下午7:56
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

/* @var $order Order */
?>


<table class="layui-table" lay-skin="line">
    <tr>
        <td>订单ID</td>
        <td><?= $order->id ?></td>
    </tr>
    <tr>
        <td>标题</td>
        <td><?= $order->title ?></td>
    </tr>
    <tr>
        <td>科目</td>
        <td><?= $order->qType->name ?></td>
    </tr>
    <tr>
        <td>时长</td>
        <td><?= $order->hour ?>小时</td>
    </tr>
    <tr>
        <td>价格</td>
        <td><?= $order->price / 100 ?>元</td>
    </tr>
    <tr>
        <td>商户订单号</td>
        <td><?= $order->out_trade_no ?></td>
    </tr>
    <tr>
        <td>微信流水号</td>
        <td><?= $order->trade_no ?></td>
    </tr>
    <tr>
        <td>下单时间</td>
        <td><?= date("Y-m-d H:i:s", $order->created_at) ?></td>
    </tr>
    <tr>
        <td>支付时间</td>
        <td><?= date("Y-m-d H:i:s", strtotime($order->paytime)) ?></td>
    </tr>
    <tr>
        <td>订单当前状态</td>
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
    </tr>
    <tr>
        <?php if ($order->status == Status::IS_PAY): ?>
        <td>
            <button class="layui-btn layui-btn-warm">退款</button>
        </td>
        <td>
        <?php else: ?>
        <td colspan="2">
        <?php endif; ?>
            <button class="layui-btn layui-btn-primary">去微信查询订单支付情况</button>
        </td>
    </tr>
</table>