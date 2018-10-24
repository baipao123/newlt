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
        <label class="layui-form-label"><?= $type->tid == 0 ? "名称" : "大题标题" ?></label>
        <div class="layui-input-block">
            <input type="text" name="name" placeholder="名称" autocomplete="off" class="layui-input" lay-filter="name"
                   lay-verify="required" value="<?= $type->name ?>">
            <?php if($type->tid>0) :?>
            <div class="layui-form-mid layui-word-aux">标题用于小程序练习的筛选，以及小后台添加题目时选择，请简单明了，如果其他说明，可以在下方添加</div>
            <?php endif;?>
        </div>
    </div>

    <?php if($type->tid == 0):?>
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
    <?php else:?>
        <div class="layui-form-item">
            <label class="layui-form-label">大题标题说明</label>
            <div class="layui-input-block">
                <input type="text" name="description" placeholder="大题标题说明" autocomplete="off" class="layui-input" lay-filter="name"
                       lay-verify="required" value="<?= $type->description ?>">
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


    <fieldset class="layui-elem-field" style="margin-top: 20px;">
        <legend>模考相关</legend>
        <div class="layui-field-box">
            <?php if ($type->tid == 0): ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">模考时间(分钟)</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" placeholder="模考时间" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->time ?>">
                        <div class="layui-form-mid layui-word-aux">该科目考试总计时间</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">总分</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" placeholder="总分" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->score ?>">
                        <div class="layui-form-mid layui-word-aux">考试满分分数</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">及格分</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" placeholder="及格分" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->passScore ?>">
                        <div class="layui-form-mid layui-word-aux">考试及格分数</div>
                    </div>
                </div>
            <?php else:?>
                <?php if ($type->parentId == 0): ?>
                    <div class="layui-form-mid" style="color:red">当科目中添加了1个或多个小题时，此处设置无效，请去小题里面设置模考相关内容</div>
                <?php endif; ?>
            <div class="layui-form-item">
                <label class="layui-form-label">模考题量</label>
                <div class="layui-input-block">
                    <input type="number" name="sort" placeholder="模考题量" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->examNum ?>">
                    <div class="layui-form-mid layui-word-aux">考试中，有几道题目，例：比如当前有2道阅读理解，共10小题，此处填2</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">每小题的分值</label>
                <div class="layui-input-block">
                    <input type="number" name="sort" placeholder="总分" autocomplete="off" class="layui-input" lay-filter="sort" value="<?= $type->score ?>">
                    <div class="layui-form-mid layui-word-aux">试卷中每小题的分值，比如当前有2道阅读理解，共10小题，每题1分，此处就填1</div>
                </div>
            </div>
            <?php endif;?>
        </div>
    </fieldset>

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