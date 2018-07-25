<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/17
 * Time: 下午8:40
 */
use layuiAdm\tools\Url;
use common\models\User;
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
    "options" => [
        "name"        => "name",
        "value"       => $name,
        "placeholder" => "微信昵称"
    ]
]);
FormWidget::end();

TableWidget::begin([
    "header"       => [
        "ID"   => ["fixed" => "left", "width" => 80, "unresize" => true],
        "微信昵称" => ['minWidth' => 110],
        "头像"   => ['minWidth' => 150],
        "操作"   => ["fixed" => "right", "minWidth" => 250, "unresize" => true]
    ],
    "height"       => 500,
    "cellMinWidth" => 60,
    "limit"        => $pagination->pageSize,
]);

/* @var $records User[] */
foreach ($records as $user) {
    ?>
    <tr>
        <td><?= $user->id ?></td>
        <td><?= $user->nickname ?></td>
        <td><img class="img" src="<?= $user->avatar ?>"/></td>
        <td>
           
            <span class="layui-btn layui-btn-warm layui-btn-xs"
                  onclick="globalOpenIFrame('/order/list?uid=<?= $user->id ?>','用户信息','my-icon-long-arrow-right')">订单记录</span>
            <span class="layui-btn layui-btn-xs"
                  onclick="globalOpenIFrame('/question/exam?uid=<?= $user->id ?>','用户信息')">模考记录</span>
        </td>
    </tr>
    <?php
}
TableWidget::end();
echo PagesWidget::widget(["pagination" => $pagination]);
?>
