<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/22
 * Time: 下午7:03
 */
use common\models\QuestionType;
use common\tools\Status;
use layuiAdm\tools\Url;

/* @var $qType QuestionType*/
?>
<button class="layui-btn layui-btn-danger"
        onclick="layerOpenIFrame('<?= Url::createLink('/question/type-info', ["pid" => $tid]) ?>','添加科目')"><i class="layui-icon">&#xe654;</i>添加<?= $tid == 0 ? '科目' : ($qType->tid == 0 ? '模考题型' : '小题类型')?>
</button>

<table class="layui-table">
    <thead>
    <tr>
        <th>科目ID</th>
        <th>科目名称</th>
        <?php if ($tid <= 0): ?>
        <th>图标</th>
        <?php endif; ?>
        <th>排序值</th>
        <th>状态</th>
        <th>最后修改时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $types QuestionType[] */ ?>
    <?php foreach ($types as $type): ?>
        <tr>
            <td><?= $type->id ?></td>
            <td><?= $type->name ?></td>
            <?php if ($type->tid == 0): ?>
            <td class="icon-<?= $type->id ?>"><img class="img" src="<?= $type->icon(true) ?>"/>
            <?php endif; ?>
            </td>
            <td><?= $type->sort ?></td>
            <td>
                <?php if($type->status == Status::PASS): ?>
                    <span class="layui-badge layui-bg-green">通过</span>
                <?php elseif($type->status == Status::FORBID):?>
                    <span class="layui-badge layui-bg-red">不通过</span>
                <?php endif;?>
            </td>
            <td><?= date("Y-m-d H:i:s", $type->updated_at) ?> </td>
            <td>
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerOpenIFrame('<?= Url::createLink("question/type-info",["tid"=>$type->id])?>','编辑科目')"><i class="layui-icon">&#xe642;</i>编辑</span>
                <?php if ($type->parentId == 0): ?>
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerOpenIFrame('<?= Url::createLink("question/types",["tid"=>$type->id])?>','<?= $type->tid == 0 ? '模考题型' : '小题类型（一个大题下面有多个不同类的小题）'?>','100%')"><i class="my-icon my-icon-list-light"></i><?= $type->tid == 0 ? '模考题型' : '小题类型'?></span>
                <?php endif; ?>
                <?php if($type->status == Status::FORBID): ?>
                    <span class="layui-btn layui-btn-sm" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::PASS])?>')">通过</span>
                    <span class="layui-btn layui-btn-sm layui-btn-primary" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::DELETE])?>','确定删除？删除后无法恢复，且题库也会删除')">删除</span>

                <?php elseif($type->status == Status::PASS):?>
                    <span class="layui-btn layui-btn-sm layui-btn-warm" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::FORBID])?>')">不通过</span>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(".img").click(function (e) {
        let classTxt = $(this).parent().eq(0).attr("class");
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>