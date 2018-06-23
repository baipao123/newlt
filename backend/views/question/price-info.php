<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/22
 * Time: 下午9:07
 */
use common\models\QuestionType;
use common\models\QuestionPrice;
use common\tools\Status;
use layuiAdm\tools\Url;

/* @var $types QuestionType[]
 * @var $price QuestionPrice
 */

?>


<style>
    .imgList > img {
        margin: 10px 0;
        max-width: 200px;
    }

</style>
<form class="layui-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

    <div class="layui-form-item">
        <label class="layui-form-label">分类</label>
        <div class="layui-input-block">
            <select name="tid" title="" lay-verify="required" lay-filter="tid">
                <option value="0">请选择分类</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type->id ?>" <?= $type->id == $price->tid ? "selected" : ""?> data-icon="<?= $type->icon ?>"><?= $type->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" placeholder="标题" autocomplete="off" class="layui-input" lay-filter="title"
                   lay-verify="required" value="<?= $price->title ?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图标</label>
        <div class="layui-input-block">
            <input type="hidden" name="cover" lay-filter="cover" lay-verify="cover" class="price-cover" value="<?= $price->cover ?>">
            <?php echo \layuiAdm\widgets\QiNiuUploaderWidget::widget(["isMulti" => false,"hint"=>"推荐尺寸:200*200"]) ?>
            <div class="imgList">
                <img src="<?= $price->cover(true) ?>" class="price-cover">
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">价格</label>
        <div class="layui-input-block">
            <input type="text" name="price" placeholder="¥" autocomplete="off" class="layui-input" lay-filter="price" lay-verify="decimal" value="<?= $price->price > 0 ? $price->price / 100 : "" ?>">
            <div class="layui-form-mid layui-word-aux">单位：元</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">原价</label>
        <div class="layui-input-block">
            <input type="text" name="oldPrice" placeholder="¥" autocomplete="off" class="layui-input" lay-filter="oldPrice" lay-verify="decimal" value="<?= $price->oldPrice > 0 ? $price->oldPrice / 100 : "" ?>">
            <div class="layui-form-mid layui-word-aux">单位：元；不填或填0则不显示</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">时长类型</label>
        <div class="layui-input-block">
            <input type="radio" name="type" value="<?= QuestionPrice::Type_Day ?>" title="天"  <?= $price->type == QuestionPrice::Type_Day || $price->isNewRecord ? "checked" : "" ?> lay-verify="required">
            <input type="radio" name="type" value="<?= QuestionPrice::Type_Hour ?>" title="小时" <?= $price->type == QuestionPrice::Type_Hour ? "checked" : "" ?> lay-verify="required">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">时长</label>
        <div class="layui-input-block">
            <input type="number" name="hour" placeholder="" autocomplete="off" class="layui-input" lay-filter="hour" lay-verify="required" value="<?= $price->type == QuestionPrice::Type_Day ? round($price->hour / 24) : $price->hour ?>">
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">购买说明</label>
        <div class="layui-input-block">
            <textarea placeholder="" class="layui-textarea" name="note"><?= $price->note ?></textarea>
            <div class="layui-form-mid layui-word-aux">不填则不显示</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">排序值</label>
        <div class="layui-input-block">
            <input type="number" name="sort" placeholder="排序值" autocomplete="off" class="layui-input" lay-filter="sort" lay-verify="number" value="<?= $price->sort ?>">
            <div class="layui-form-mid layui-word-aux">越大越靠前</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">上架时间</label>
        <div class="layui-input-block">
            <input type="text" class="layui-input datetime" name="datetime" value="<?= $price->isNewRecord || ($price->start_at == 0 && $price->end_at == 0) ? "" : ($price->start_at > 0 ? date("Y-m-d H:i:s", $price->start_at) : "") . " - " . (($price->end_at > 0 ? date("Y-m-d H:i:s", $price->end_at) : ""))?>" lay-filter="datetime">
            <div class="layui-form-mid layui-word-aux">不填表示永久上架</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="<?= Status::PASS ?>" title="上架"  <?= $price->status == Status::PASS || $price->isNewRecord ? "checked" : "" ?> lay-verify="required">
            <input type="radio" name="status" value="<?= Status::FORBID ?>" title="下架"  <?= $price->status == Status::FORBID ? "checked" : "" ?> lay-verify="required">
            <input type="radio" name="status" value="<?= Status::EXPIRE ?>" title="已过期"  <?= $price->status == Status::EXPIRE ? "checked" : "" ?> lay-verify="required">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="submit">立即提交</button>
        </div>
    </div>
</form>
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        let form = layui.form,
            layer = layui.layer,
            layDate = layui.laydate
        form.render();
        layDate.render({
            elem: '.datetime'
            ,type: 'datetime'
            ,range: true
        });

        form.on('select(tid)', function (data) {
            console.log(data);
            let e = data.elem,
                tid = data.value,
                option = $(e).find("option[value=" + tid + "]"),
                coverInput = $("input.price-cover")
            console.log(option)
            console.log(coverInput.val())
            if (coverInput.val() == "")
                uploadFile({key: option.attr("data-icon")}, true)
        });
        form.verify()
    });

    function uploadFile(info,inputNull) {
        let coverImg = $("img.price-cover"),
            coverInput = $("input.price-cover")
        coverImg.attr("src", "<?=Yii::$app->params['qiniu']['domain']?>" + "/" + info.key + "-icon")
        if (!inputNull)
            coverInput.val(info.key)
        coverImg.removeClass("hide")
    }

</script>