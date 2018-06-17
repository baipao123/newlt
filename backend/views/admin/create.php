<?php /* @var $this \yii\web\View */ ?>
<form class="layui-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
    <div class="layui-form-item">
        <input type="text" name="aName" lay-verify="name" placeholder="用户名" autocomplete="off" class="layui-input">
    </div>
    <div class="layui-form-item">
        <input type="text" name="aPwd" lay-verify="pwd" placeholder="密码" autocomplete="off" class="layui-input" >
    </div>
    <div class="layui-form-item m-login-btn">
        <button class="layui-btn layui-btn-normal login-btn" lay-submit lay-filter="reset">确认添加</button>
    </div>
</form>
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        var form = layui.form,
            layer = layui.layer;
        form.render();
        //自定义验证规则
        form.verify({
            pwd: [/(.+){6,12}$/, '密码必须6到12位'],
            name: [/(.+){6,12}$/, '用户名必须6到12位'],
        });
    });
</script>
</script>
