<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:49
 */

use common\models\QuestionPrice;
use common\models\QuestionType;
use common\tools\Status;
use layuiAdm\tools\Url;
/* @var $types QuestionType[] */
/* @var $prices QuestionPrice[] */
?>


<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">

            <div class="layui-input-inline">
                <select name="tid" title="">
                    <option value="0">请选择分类</option>
                    <?php foreach ($types as $type): ?>
                    <option value="<?= $type->id ?>" <?= $type->id == $tid ? "selected" : ""?>><?= $type->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="layui-input-inline">
                <select name="status" title="">
                    <option value="0">全部状态</option>
                    <option value="<?= Status::PASS ?>" <?= $status == Status::PASS ? "selected" : ""?>>上架</option>
                    <option value="<?= Status::FORBID ?>" <?= $status == Status::FORBID ? "selected" : ""?>>下架</option>
                    <option value="<?= Status::EXPIRE ?>" <?= $status == Status::EXPIRE ? "selected" : ""?>>已过期</option>
                </select>
            </div>

            <div class="layui-inline">
                <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
            </div>
        </form>
    </div>
</fieldset>

<button class="layui-btn layui-btn-danger"
        onclick="layerOpenIFrame('<?= Url::createLink('/question/price-info', ["id" => 0]) ?>','添加价格')"><i class="layui-icon">&#xe654;</i>添加价格
</button>


<table class="layui-table simple">
    <thead>
    <tr>
        <th>分类ID</th>
        <th>标题</th>
        <th>图标</th>
        <th>价格</th>
        <th>时间</th>
        <th>排序值</th>
        <th style="width: 100px">商品描述</th>
        <th style="width: 100px">上架时间</th>
        <th style="min-width:50px; width: 80px;">状态</th>
        <th style="width: 100px">最后修改时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($prices as $price): ?>
        <tr>
            <td><?= $price->id ?></td>
            <td><?= $price->title ?></td>
            <td class="icon-<?= $price->id ?>"><img class="img" src="<?= $price->cover(true) ?>" style="width: 50px;"/></td>
            <td><?= $price->price / 100 ?> 元<?= $price->oldPrice > 0 ? "<Br><s>原价：" . ($price->oldPrice / 100) . " 元</s>" : "" ?></td>
            <td><?= $price->hourStr() ?></td>
            <td><?= $price->sort ?></td>
            <td><?= $price->note ?></td>
            <td><?= $price->timeStr() ?></td>
            <td>
                <?php if($price->status == Status::PASS): ?>
                    <span class="layui-badge layui-bg-green">已上架</span>
                <?php elseif($price->status == Status::FORBID):?>
                    <span class="layui-badge layui-bg-red">已下架</span>
                <?php elseif($price->status == Status::EXPIRE):?>
                    <span class="layui-badge layui-bg-orange">已结束</span>
                <?php endif;?>
            </td>
            <td><?= date("Y-m-d H:i:s", $price->updated_at) ?> </td>
            <td>
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerOpenIFrame('<?= Url::createLink("question/price-info",["id"=>$price->id])?>','编辑价格')"><i class="layui-icon">&#xe642;</i>编辑</span>
                <?php if($price->status == Status::FORBID): ?>
                    <span class="layui-btn layui-btn-sm" onclick="layerConfirmUrl('<?= Url::createLink("question/price-toggle",["tid"=>$price->id,"status"=>Status::PASS])?>')">上架</span>
                    <span class="layui-btn layui-btn-sm layui-btn-primary" onclick="layerConfirmUrl('<?= Url::createLink("question/price-toggle",["tid"=>$price->id,"status"=>Status::DELETE])?>','确定删除？删除后无法恢复')">删除</span>
                <?php elseif($price->status == Status::PASS):?>
                    <span class="layui-btn layui-btn-sm layui-btn-warm" onclick="layerConfirmUrl('<?= Url::createLink("question/price-toggle",["tid"=>$price->id,"status"=>Status::FORBID])?>')">下架</span>
                <?php endif;?>
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