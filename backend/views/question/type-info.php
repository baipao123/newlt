<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/22
 * Time: 下午9:07
 */
use common\models\QuestionType;
use common\tools\Status;
use layuiAdm\tools\Url;

/* @var $type QuestionType */
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
        <label class="layui-form-label">名称</label>
        <div class="layui-input-block">
            <input type="text" name="name" placeholder="名称" autocomplete="off" class="layui-input" lay-filter="name"
                   lay-verify="required" value="<?= $type->name ?>">
        </div>
    </div>

    <?php if($type->parentId == 0):?>
    <div class="layui-form-item">
        <label class="layui-form-label">图标</label>
        <div class="layui-input-block">
            <input type="hidden" name="icon" lay-filter="icon" lay-verify="icon" class="type-icon" value="<?= $type->icon ?>">
            <?php echo \layuiAdm\widgets\QiNiuUploaderWidget::widget(["isMulti" => false,"hint"=>"推荐尺寸:200*200"]) ?>
            <div class="imgList">
                <img src="<?= $type->icon(true) ?>" class="type-icon <?= $type->icon == "" ? "hide" : ""?>">
            </div>
        </div>
    </div>
    <?php endif;?>

    <div class="layui-form-item">
        <label class="layui-form-label">排序值</label>
        <div class="layui-input-block">
            <input type="number" name="sort" placeholder="排序值" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->sort ?>">
            <div class="layui-form-mid layui-word-aux">越大越靠前</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">是否开启</label>
        <div class="layui-input-block">
            <input type="checkbox" name="status" lay-skin="switch" <?= $type->status == Status::PASS ? "checked" : "" ?> lay-filter="status" >
        </div>
    </div>

    <?php if ($type->parentId > 0): ?>
    <fieldset class="layui-elem-field" style="margin-top: 20px;">
        <legend>模考相关</legend>
        <div class="layui-field-box">
            <div class="layui-form-item">
                <label class="layui-form-label">分值</label>
                <div class="layui-form-mid">总分:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[totalScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("totalScore") ?>">
                </div>
                <div class="layui-form-mid"> 合格:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[passScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("passScore") ?>">
                </div>
                <div class="layui-form-mid">分</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">判断题</label>
                <div class="layui-form-mid">数量:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[judgeNum]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("judgeNum") ?>">
                </div>
                <div class="layui-form-mid"> 每道:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[judgeScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("judgeScore") ?>">
                </div>
                <div class="layui-form-mid">分</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">单选题</label>
                <div class="layui-form-mid">数量:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[selectNum]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("selectNum") ?>">
                </div>
                <div class="layui-form-mid"> 每道:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[selectScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("selectScore") ?>">
                </div>
                <div class="layui-form-mid">分</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">多选题</label>
                <div class="layui-form-mid">数量:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[multiNum]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("multiNum") ?>">
                </div>
                <div class="layui-form-mid"> 每道:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[multiScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("multiScore") ?>">
                </div>
                <div class="layui-form-mid">分</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">填空题</label>
                <div class="layui-form-mid">数量:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[blankNum]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("blankNum") ?>">
                </div>
                <div class="layui-form-mid"> 每道:</div>
                <div class="layui-input-inline" style="width: 60px;">
                    <input type="number" name="setting[blankScore]" autocomplete="off" class="layui-input" title="" value="<?= $type->setting("blankScore") ?>">
                </div>
                <div class="layui-form-mid">分</div>
            </div>
        </div>
    </fieldset>
    <?php endif; ?>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="submit">立即提交</button>
        </div>
    </div>
</form>
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        let form = layui.form,
            layer = layui.layer
        form.render();

        form.verify({
            icon: function (value) {
                console.log(value)
                if (value == "")
                    return "请上传图标";
            }
        })
    });

    function uploadFile(info) {
        console.log(info)
        let coverImg = $("img.type-icon"),
            coverInput = $("input.type-icon")
        coverImg.attr("src", "<?=Yii::$app->params['qiniu']['domain']?>" + "/" + info.key + "-icon")
        coverInput.val(info.key)
        coverImg.removeClass("hide")
    }

</script>