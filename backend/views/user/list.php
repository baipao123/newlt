<style>
    img.img{
        max-width:60px;
    }
</style>

<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="name" placeholder="用户姓名" autocomplete="off" class="layui-input" value="<?= $name ?>">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="phone" placeholder="手机号" autocomplete="off" class="layui-input" value="<?= $phone ?>">
                    </div>
                </div>

                <div class="layui-inline" style="width: 100px;">
                    <select name="gender" lay-verify="gender">
                        <option value="-1">选择性别</option>
                        <option value="1" <?= $gender == 1 ? "selected" : ""?>>男</option>
                        <option value="2" <?= $gender == 2 ? "selected" : ""?>>女</option>
                        <option value="0" <?= $gender == 0 ? "selected" : ""?>>未知</option>
                    </select>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
                </div>
            </div>
        </form>
    </div>
</fieldset>

<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>姓名</th>
        <th>头像</th>
        <th>手机号</th>
        <th>首选城市</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\User[] */ ?>
    <?php foreach ($records as $user): ?>
        <tr>
            <td><?= $user->id ?></td>
            <td><?= $user->realname ?></td>
            <td class="icon-<?= $user->id ?>">
                <img class="img" src="<?= $user->avatar ?>"/>
            </td>
            <td><?= $user->phone ?></td>
            <td><?= $user->cityStr() ?></td>
            <td>

            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>

<script>
    $(".img").click(function (e) {
        var classTxt = $(this).parent().eq(0).attr("class");
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>
