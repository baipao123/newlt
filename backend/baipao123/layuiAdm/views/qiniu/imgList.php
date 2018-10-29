<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午8:47
 */
?>
<?= $uploader ?>
<style>
    .imgList .single-img{
        display: inline-block;
        margin-right: 10px;
    }
    .imgList .single-img a{
        display: block;
    }
</style>
<div class="imgList imgList-<?= $id ?>" style="padding: 10px 0;">
    <?php foreach ($img as $key): ?>
        <div class="single-img single-img-<?= $id ?>">
            <img src="<?= Yii::$app->params['qiniu']['domain'] . '/' . $key ?>">
            <?php if ($multi): ?>
                <a href="javascript:void(0)" class="layui-btn layui-btn-primary layui-btn-xs" onclick="imgListDelete<?=$id?>($(this))">删除</a>
            <?php endif; ?>
            <input type="hidden" name="<?= $name ?><?= $multi ? '[]':'' ?>" value="<?= $key ?>">
        </div>
    <?php endforeach; ?>
</div>
<script>
    function imgListDelete<?=$id?>(obj) {
        globalLayer.confirm("确定删除此图片?", {}, function () {
            obj.parent().remove();
            globalLayer.msg('已删除图片');
        })
    }

    function uploadFile<?=$id?>(info) {
        var classText = "single-img-<?=$id?>",
            qiNiuUrl = "<?= Yii::$app->params['qiniu']['domain']?>";
        <?php if ($multi): ?>
            addImg<?=$id?>(info)
            imgPreview<?=$id?>()
        <?php else:?>
            var div = $("." + classText);
            if(div.length>0) {
                div.find("img").attr("src", qiNiuUrl + '/' + info.key);
                div.find("input").val(info.key);
            }else{
                addImg<?=$id?>(info)
                imgPreview<?=$id?>()
            }
        <?php endif; ?>

    }

    function addImg<?=$id?>(info) {
        var classText = "imgList-<?=$id?>",
            html = '';
        html += '<div class="single-img single-img-<?= $id ?>">';
        html += '<img src="<?= Yii::$app->params['qiniu']['domain']  ?>/'+info.key+'">';
        <?php if ($multi): ?>
        html += '<a href="javascript:void(0)" class="layui-btn layui-btn-primary layui-btn-xs" onclick="imgListDelete<?=$id?>($(this))">删除</a>';
        <?php endif;?>
        html += '<input type="hidden" name="<?= $name ?>[]" value="'+info.key+'">';
        $("."+classText).append(html)
    }
    
    function imgPreview<?=$id?>() {
        var classText = "single-img-<?=$id?>",
            img = $("." + classText + " img");
        img.unbind("click")
        img.click(function () {
            console.log(123)
            thisLayer.photos({
                photos: "." + classText
            })
        })
    }
    imgPreview<?=$id?>();
</script>