<?php
/* @var $this Yii::$app->view */
?>

<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">账户名</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="username" autocomplete="off" class="layui-input" value="<?= $username ?>">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">用户状态</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <select name="status" lay-search="">
                            <option value="" <?= $status === "" ? "selected" : "" ?>>全部</option>
                            <option value="1" <?= $status == 1 ? "selected" : "" ?>>禁用</option>
                            <option value="10" <?= $status == 10 ? "selected" : "" ?>>启用</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
                </div>
            </div>
        </form>
    </div>
</fieldset>

    <button class="layui-btn layui-btn-danger" onclick="layerOpenIFrame('/admin/create','添加账户')"><i class="layui-icon">&#xe654;</i>添加账户</button>

<table class="layui-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>账户名</th>
            <th>创建时间</th>
            <th>当前状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php /* @var $admins \common\models\Admin[] */ ?>
    <?php foreach($admins as $admin):?>
        <tr>
            <td><?= $admin->id ?></td>
            <td><?= $admin->username ?></td>
            <td><?= date("Y-m-d H:i:s", $admin->created_at) ?></td>
            <td>
            <?php if($admin->status == \common\models\Admin::STATUS_ACTIVE): ?>
                <span class="layui-btn layui-btn-sm layui-btn-normal">启用</span>
            <?php else: ?>
                <span class="layui-btn layui-btn-sm layui-btn-danger">禁用</span>
            <?php endif;?>
            </td>
            <td>
            <?php if($admin->status == \common\models\Admin::STATUS_DISABLE): ?>
                <span class="layui-btn layui-btn-sm layui-btn-normal" onclick="layerConfirmUrl('/admin/status?aid=<?=$admin->id?>&status=10')">启用</span>
                <span class="layui-btn layui-btn-sm layui-btn-primary" onclick="layerConfirmUrl('/admin/status?aid=<?=$admin->id?>&status=0','确定删除吗？<br>删除后无法恢复')">删除</span>
            <?php else: ?>
                <span class="layui-btn layui-btn-sm layui-btn-danger"onclick="layerConfirmUrl('/admin/status?aid=<?=$admin->id?>&status=1','确定禁用吗？')">禁用</span>
            <?php endif;?>
            <?php if($admin->id != Yii::$app->user->id): ?>
                <span class="layui-btn layui-btn-sm" onclick="layerConfirmUrl('/admin/reset-pwd?id=<?=$admin->id?>')">重置密码</span>
            <?php endif;?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>