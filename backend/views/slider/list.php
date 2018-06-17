<?php
use common\models\Slider;

?>

<style>
    img.img {
        max-width: 60px;
    }
</style>
<button class="layui-btn layui-btn-danger" onclick="layerOpenIFrame('/slider/info','添加轮播')"><i class="layui-icon">&#xe654;</i>添加账户</button>
<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>标题</th>
        <th>封面</th>
        <th>起止时间</th>
        <th>当前状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\Slider[] */ ?>
    <?php foreach ($records as $record): ?>
        <tr>
            <td><?= $record->id ?></td>
            <td><?= $record->title ?></td>
            <td class="cover-<?= $record->id ?>"><img class="img" src="<?= $record->cover(true) ?>"/></td>
            <td><?= $record->timeStr() ?></td>
            <td>
                <?php if ($record->status == \common\models\Slider::STATUS_ON) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-normal">轮播中</span>
                <?php elseif ($record->status == \common\models\Slider::STATUS_EXPIRE) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-warm">已结束</span>
                <?php elseif ($record->status == \common\models\Slider::STATUS_OFF) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-danger">已关闭</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="layui-btn layui-btn-sm"
                      onclick="layerOpenIFrame('/slider/info?id=<?= $record->id ?>','编辑轮播')">编辑</span>
                <?php if ($record->status == \common\models\Slider::STATUS_ON) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-danger"
                          onclick="layerConfirmUrl('/slider/toggle-status?id=<?= $record->id ?>&status=<?= Slider::STATUS_OFF ?>')">下架</span>
                <?php elseif ($record->status == \common\models\Slider::STATUS_OFF) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-normal"
                          onclick="layerConfirmUrl('/slider/toggle-status?id=<?= $record->id ?>&status=<?= Slider::STATUS_ON ?>')">上架</span>
                <?php endif; ?>
                <?php if ($record->status == \common\models\Slider::STATUS_OFF) : ?>
                    <span class="layui-btn layui-btn-sm layui-btn-primary"
                          onclick="layerConfirmUrl('/slider/toggle-status?id=<?= $record->id ?>&status=<?= Slider::STATUS_DEL ?>','确认删除本条轮播？删除后无法恢复')">删除</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>

<script>
    $(".img").click(function (e) {
        let classTxt = $(this).parent().eq(0).attr("class");
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>