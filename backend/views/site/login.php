<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        html,body{height:100%;}
        .login-box{position: absolute;left: 50%;top:50%;transform: translate(-50%,-50%);}
        .login{background-color:rgba(255,255,255,0.5);padding:20px;}
        .login-title{text-align: center;margin-bottom:20px}
        button.login-btn{position: absolute;right: 0;}
    </style>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="layui-bg-black" style="height: 100%">
        <div class="layui-row">
            <div class="login-box layui-col-xs10 layui-col-sm6 layui-col-md4 layui-col-lg4">
                <div class="login">
                    <h2 class="login-title">后台系统登录</h2>
                    <div class="login-warp">
                        <form class="layui-form" method="post">
                            <div class="layui-form-item">
                                <input type="text" name="username" lay-verify="username" placeholder="用户名" autocomplete="off" class="layui-input" value="<?= $username ?>">
                            </div>
                            <div class="layui-form-item">
                                <input type="password" name="password" lay-verify="password" placeholder="密码" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-item m-login-btn">
                                <div class="layui-row">
                                    <div class="layui-col-xs8">
                                        <input type="checkbox" name="remember" title="记住密码">
                                    </div>
                                    <div class="layui-col-xs4">
                                        <button class="layui-btn layui-btn-normal login-btn" lay-submit lay-filter="login">登录</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="copyright">Copyright 2015-2016 by XIAODU</p>
            </div>
        </div>
    </div>
    <script>
        //JavaScript代码区域
        layui.use('element', function(){
            var element = layui.element;
            element.init();
        });

        layui.use(['form', 'layedit', 'laydate'], function () {
            var form = layui.form,
                layer = layui.layer;
            form.render();
            <?php if(!empty($error)):?>
            layer.alert("<?= $error ?>",{
                icon:2,
                btn:false,
                time:1500,
                title:false,
                closeBtn:0
            });
            <?php endif;?>
            //自定义验证规则
            form.verify({
                username: function (value) {
                    if (value.length < 5) {
                        console.log(123);
                        return '用户名至少得5个字符';
                    }
                },
                password: [/(.+){6,12}$/, '密码必须6到12位'],
            });
        });
    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>