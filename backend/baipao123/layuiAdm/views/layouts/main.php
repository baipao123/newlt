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
<body>
<?php $this->beginBody() ?>
<div class="container" style="padding: 15px;">
    <div class="layui-row">
        <?= $content ?>
    </div>
</div>
<script>
    var globalLayer;
    $(document).ready(function(){
        //有无parent  是不是在iFrame中
        if (window.frames.length === parent.frames.length) {
            var cur = {
                title: $(document).title,
                url: window.location.href
            }
            window.sessionStorage.setItem("bp-curmenu", JSON.stringify(cur));
            window.location.href = "/";
        }

        layui.use(['element','form', 'layer'], function () {
            var form = layui.form,
                element = layui.element;
            globalLayer = layui.layer;
            element.init();
            form.render();
        });
    });

    function layerConfirmUrl(url, text, _target) {
        if (text === undefined || text === '')
            return layerOpenIFrame(url);
        globalLayer.confirm(text, function () {
            if (_target === undefined || !_target)
                layerOpenIFrame(url);
            else
                window.location.href = url;
        });
    }

    function layerOpenIFrame(url, title, width) {
        width = width === undefined || width === "" ? ((title === "" || title === undefined) ? '235px' : '380px' ) : width;
        globalLayer.open({
            type: 2,
            title: title,
            shadeClose: false,
            area: width,
            maxmin: true,
            content: url
        });
    }

    function globalOpenIFrame(url, title, icon) {
        window.parent.globalBodyTab.tabAddiFrame(title, icon, url)
    }

</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
