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

?>


<table class="layui-table">
    <thead>
    <tr>
        <th>分类ID</th>
        <th>名称</th>
        <th>图标</th>
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
            <td class="icon-<?= $type->id ?>"><img class="img" src="<?= $type->icon(true) ?>"/>
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
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerOpenIFrame('<?= Url::createLink("question/type-info",["tid"=>$type->id])?>','编辑分类')"><i class="layui-icon">&#xe642;</i>编辑</span>
                <?php if($type->status == Status::FORBID): ?>
                    <span class="layui-btn layui-btn-sm" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::PASS])?>')">通过</span>
                    <span class="layui-btn layui-btn-sm layui-btn-primary" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::DELETE])?>','确定删除？删除后无法恢复')">删除</span>

                <?php elseif($type->status == Status::PASS):?>
                    <span class="layui-btn layui-btn-sm layui-btn-warm" onclick="layerConfirmUrl('<?= Url::createLink("question/type-toggle",["tid"=>$type->id,"status"=>Status::FORBID])?>')">不通过</span>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>