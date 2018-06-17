<?php
/* @var $this \yii\web\View */
/* @var $slider Slider */
use common\models\Slider;

?>
<style>
    .imgList > img {
        margin: 10px 0;
        max-width: 200px;
    }

    .hide {
        display: none;
    }
</style>
<form class="layui-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
    <div class="layui-form-item">
        <div class="layui-form-mid">标题</div>
        <input type="text" name="title" lay-filter="title" placeholder="标题" autocomplete="off" class="layui-input"
               value="<?= $slider->title ?>">
        <div class="layui-form-mid layui-word-aux">不填则不显示</div>
    </div>

    <div class="layui-form-item">
        <div class="layui-col-xs12">
            <div class="layui-form-mid">图片</div>
            <div class="layui-form-mid layui-word-aux">推荐尺寸：750*375</div>
        </div>
        <input type="hidden" name="cover" lay-filter="cover" lay-verify="cover" class="slider-cover" value="<?= $slider->cover ?>">
        <div class="layui-col-xs12">
            <?php echo \layuiAdm\widgets\QiNiuUploaderWidget::widget(["isMulti" => false]) ?>
        </div>
        <div class="imgList">
            <img src="<?= $slider->cover(true) ?>" class="slider-cover <?= $slider->cover == "" ? "hide" : ""?>">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">跳转目标</div>
        <div class="layui-col-xs12">
            <input type="radio" name="type" value="0" lay-filter="type"  lay-verify="type"
                   title="无跳转" <?= $slider->type == Slider::TYPE_NONE ? "checked" : "" ?>>
            <input type="radio" name="type" value="1" lay-filter="type"  lay-verify="type"
                   title="岗位" <?= $slider->type == Slider::TYPE_JOB ? "checked" : "" ?>>
            <input type="radio" name="type" value="3" lay-filter="type"  lay-verify="type"
                   title="外链" <?= $slider->type == Slider::TYPE_LINK ? "checked" : "" ?>>
        </div>
        <div class="layui-form-mid layui-word-aux">外链需要在小程序内将域名加入业务域名的白名单内</div>
    </div>

    <div class="layui-form-item job <?= $slider->type != Slider::TYPE_JOB ? "hide" : "" ?>">
        <div class="layui-form-mid">岗位ID</div>
        <input type="number" name="tid" lay-filter="title" placeholder="岗位ID" autocomplete="off" class="layui-input"
               value="<?= $slider->tid ?>">
        <div class="layui-form-mid layui-word-aux">前往岗位列表查看</div>
    </div>

    <div class="layui-form-item link <?= $slider->type != Slider::TYPE_LINK ? "hide" : "" ?>">
        <div class="layui-form-mid">外链网址</div>
        <input type="text" name="link" lay-filter="title" placeholder="外链网址" autocomplete="off" class="layui-input"
               value="<?= $slider->link ?>">
        <div class="layui-form-mid layui-word-aux">必须https域名</div>
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">开始时间</div>
        <input type="text" class="layui-input laydate" id="date_start" name="start_date"
               placeholder="<?= date("Y-m-d") ?>"
               value="<?= empty($slider->start_at) ? "" : date("Y-m-d", $slider->start_at) ?>">
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">结束时间</div>
        <input type="text" class="layui-input laydate" id="date_end" name="end_date" placeholder="永久生效"
               value="<?= empty($slider->end_at) ? "" : date("Y-m-d", $slider->end_at) ?>">
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">排序值</div>
        <input type="text" name="sort" lay-filter="sort" placeholder="排序值" autocomplete="off" class="layui-input"
               value="<?= $slider->sort ?>">
        <div class="layui-form-mid layui-word-aux">越大越靠前</div>
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">状态</div>
        <div class="layui-col-xs12">
            <input type="radio" name="status" value="1" lay-filter="status"  lay-verify="status"
                   title="上架" checked>
            <input type="radio" name="status" value="2" lay-filter="status"  lay-verify="status"
                   title="下架" <?= $slider->status == Slider::STATUS_OFF ? "checked" : "" ?>>
        </div>
        <div class="layui-form-mid layui-word-aux">外链需要在小程序内将域名加入业务域名的白名单内</div>
    </div>


    <div class="layui-form-item m-login-btn">
        <button class="layui-btn layui-btn-normal login-btn" lay-submit lay-filter="submit">保存</button>
    </div>
</form>
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        let form = layui.form,
            layer = layui.layer,
            laydate = layui.laydate
        form.render();
        laydate.render({
            elem: '#date_start' //指定元素
        });
        laydate.render({
            elem: '#date_end' //指定元素
        });
        form.on("radio(type)", function (e) {
            let type = e.value,
                jobInputBlock = $(".layui-form-item.job"),
                linkInputBlock = $(".layui-form-item.link")
            if (type == 0) {
                jobInputBlock.addClass("hide")
                linkInputBlock.addClass("hide")
            } else if (type == 1) {
                jobInputBlock.removeClass("hide")
                linkInputBlock.addClass("hide")
            } else if (type == 3) {
                jobInputBlock.addClass("hide")
                linkInputBlock.removeClass("hide")
            }
        })

        form.verify({
            cover: function (value) {
                console.log(value)
                if (value == "")
                    return "请上传轮播图";
            },
            type: function (value) {
                if (value == "")
                    return "请选择跳转类型";
            },
            status: function (value) {
                console.log(value)
                if (value == "")
                    return "请选择是否上架";
            }
        })
    });

    function uploadFile(info) {
        console.log(info)
        let coverImg = $("img.slider-cover"),
            coverInput = $("input.slider-cover")
        coverImg.attr("src", "<?=Yii::$app->params['qiniu']['domain']?>" + "/" + info.key + "?imageslim|imageMogr2/format/png/thumbnail/!750x375r/gravity/Center/crop/750x375/interlace/1")
        coverInput.val(info.key)
        coverImg.removeClass("hide")
    }

</script>

