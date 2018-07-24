<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/24
 * Time: 下午8:49
 */
use common\models\Order;
use common\tools\Status;
use layuiAdm\widgets\FormWidget;
use layuiAdm\widgets\FormItemWidget;

/* @var $order Order */


FormWidget::begin([

]);
echo FormItemWidget::widget([
    "type"    => "number",
    "options" => [
        "name"        => "oid",
        "value"       => $oid ?: false,
        "placeholder" => "订单ID",
    ]
]);

echo FormItemWidget::widget([
    "type"    => "text",
    "options" => [
        "name"        => "out_trade_no",
        "value"       => $out_trade_no ?: false,
        "placeholder" => "商户订单号",
    ]
]);

echo FormItemWidget::widget([
    "type"    => "number",
    "options" => [
        "name"        => "trade_no",
        "value"       => $trade_no ?: false,
        "placeholder" => "微信流水号",
    ]
]);

FormWidget::end();

?>


<div class="layui-col-xs12 layui-col-sm5">
    <?php if ($order): ?>
        <blockquote class="layui-elem-quote" style="text-align: center;">
            系统查询结果
        </blockquote>
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
                <td>用户</td>
                <td><?= $order->user->nickname ?></td>
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
                    <button class="layui-btn layui-btn-warm"
                            onclick="refund(<?= $order->id ?>,<?= $order->price / 100 ?>)">
                        退款
                    </button>
                </td>
                <td>
                    <?php else: ?>
                <td colspan="2">
                    <?php endif; ?>
                    <button class="layui-btn layui-btn-normal" onclick="jump(<?= $order->id ?>)">去微信查询订单支付情况</button>
                </td>
            </tr>
        </table>

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

            function jump(oid) {
                parent.globalOpenIFrame("/order/query?oid=" + oid, "查询订单", "my-icon-search")
            }
        </script>
    <?php else: ?>
        <blockquote class="layui-elem-quote" style="text-align: center;">
            在系统未找到此订单，请留意微信的订单查询结果
        </blockquote>
    <?php endif; ?>
</div>

<div class="layui-col-xs12 layui-col-sm5 layui-col-sm-offset2">
    <?php if ($data): ?>
        <blockquote class="layui-elem-quote" style="text-align: center;">
            微信查询结果
        </blockquote>
        <table class="layui-table" lay-skin="line">
            <tr>
                <td>用户标识</td>
                <td><?= $data['openid'] ?></td>
                <td>
                    <a class="clear" href="javascript:void(0)"
                       onclick="globalOpenIFrame('/user/list?openid=<?= $data['openid'] ?>','用户列表','&#xe612;')">
                        查询用户
                    </a>
                </td>
            </tr>
            <tr>
                <td>是否关注公众账号</td>
                <td><?= $data['is_subscribe'] ?></td>
                <td></td>
            </tr>
            <tr>
                <td>交易状态</td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\common\tools\WxPay::PayStatus, $data['trade_state'], "未知状态") ?></td>
                <td></td>
            </tr>
            <tr>
                <td>订单总价</td>
                <td><?= $data['total_fee'] ?>分</td>
                <td></td>
            </tr>
            <tr>
                <td>实付金额</td>
                <td><?= $data['cash_fee'] ?>分</td>
                <td></td>
            </tr>
            <tr>
                <td>微信流水号</td>
                <td><?= $data['transaction_id'] ?></td>
                <td></td>
            </tr>
            <tr>
                <td>商户订单号</td>
                <td><?= $data['out_trade_no'] ?></td>
                <td></td>
            </tr>
            <tr>
                <td>付款银行</td>
                <td><?= $data['bank_type'] == "CFT" ? "微信零钱" : $data['bank_type'] ?></td>
                <td><a href="https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=4_2"
                       target="_blank" class="clear">银行类型</a></td>
            </tr>
            <tr>
                <td>支付完成时间</td>
                <td><?= $data['time_end'] ?></td>
                <td></td>
            </tr>
            <tr>
                <td>交易状态描述</td>
                <td><?= $data['trade_state_desc'] ?></td>
                <td></td>
            </tr>
        </table>
    <?php else: ?>
        <blockquote class="layui-elem-quote" style="text-align: center;">
            在微信未找到此订单，没有发生支付事件
        </blockquote>
    <?php endif; ?>
</div>
