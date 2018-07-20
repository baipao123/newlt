<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
$assetUrl = \Yii::$app->assetManager->publish(dirname(__FILE__) . '/../../assets')[1];
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
        <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layui/css/layui.css"/>
        <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layui/css/layui.hc.css"/>
        <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layuicms2.0/css/public.css"/>
        <script type="text/javascript" charset="utf-8" src="<?= $assetUrl ?>/jQuery.3.3.1.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?= $assetUrl ?>/layui/layui.all.js"></script>
        <?php $this->head() ?>
    </head>
    <body class="">
    <?php $this->beginBody() ?>
    <div class="container" style="padding-top: 30px; padding-bottom: 30px;">
        <div class="layui-row">
            <div class="layui-col-xs10 layui-col-xs-offset1 ">
                <?= $content ?>
            </div>
        </div>
        <script>
            var globalLayer = parent.globalLayer;
            var layerIndex = globalLayer.getFrameIndex(window.name); //获取窗口索引
            var thisLayer;


            function layerConfirmUrl(url, text, _target) {
                parent.layerConfirmUrl(url, text, _target, window.name);
            }

            function layerOpenIFrame(url, title, width) {
                parent.layerOpenIFrame(url, title, width, window.name)
            }

            function globalOpenIFrame(url, title, icon) {
                parent.globalOpenIFrame(url, title, icon)
            }

            function layerMsg(text){
                if (text != undefined && text != "")
                    globalLayer.msg(text);
            }
            /**
             * 使用layer的alert弹窗
             * @params  string   text   alert的内容
             * @params  string   icon   alert分类：danger、success、warning前面的图标，info 使用msg，然后过几秒自动关闭
             * @params  bool     close  弹窗完成后，是否需要关闭本页面
             * @params  string   title  弹窗的标题，如果为false，则不显示标题
             */
            function layerAlert(text, icon, close, title) {
                if (text == undefined || text == "")
                    return false;
                if (icon == "info")
                    return layerMsg(text);
                switch (icon) {
                    case "danger":
                        icon = 2;
                        break;
                    case "success":
                        icon = 1;
                        break;
                    case "warning":
                        icon = 0;
                        break;
                    default:
                        //不显示标签
                        icon = -1;
                        break;
                }
                if (title == undefined)
                    title = "提示";
                else if (title == "")
                    title = false;
                globalLayer.alert(text, {
                    title: title,
                    icon: icon,
                    end: function (index) {
                        console.log(icon);
                        //success 的需要刷新主页面
                        if (icon == 1) {
                            if (parent.layerIFrameName == '')
                                parent.location.reload();
                            else
                                parent.frames[parent.layerIFrameName].location.reload()
                        }
                        if(close)
                            globalLayer.close(layerIndex);
                    }
                });
            }

            function layer_award(){
                globalLayer.close(layerIndex);
            }
            $(document).ready(function (e) {
                //danger、success会关闭本页面
                // warning 需要点击确认来关闭弹窗,不关闭本页面
                // info 使用msg，然后过几秒自动关闭
                <?php foreach (['danger', 'warning', 'success', 'info','award_info'] as $msg): ?>

                <?php if (Yii::$app->session->hasFlash($msg)): ?>
                <?php $msg_text = Yii::$app->session->getFlash($msg);?>
                <?php if(in_array($msg, ["danger", "success"])):?>
                layerAlert("<?=$msg_text?>", "<?=$msg?>", true);
                <?php elseif ($msg == "warning"):?>
                layerAlert("<?=$msg_text?>", "<?=$msg?>");
                <?php elseif ($msg == "award_info"):?>
                layer_award();
                <?php else:?>
                layerMsg("<?=$msg_text?>");
                <?php endif;?>
                <?php endif; ?>
                <?php endforeach;?>
//                var bodyHH =document.documentElement.clientHeight;t=t>bodyHH?bodyHH*0.9:t;

                layui.use(['element', 'form', 'layer'], function () {
                    let form = layui.form,
                        element = layui.element;
                    element.init();
                    form.render();
                    thisLayer = layui.layer;
                });

                //高度自适应
                globalLayer.iframeAuto(layerIndex);
            });
        </script>

    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>