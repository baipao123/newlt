        <div class="layui-btn bp-qiniu-uploader bp-qiniu-uploader-<?= $id ?>">
            <i class="layui-icon">&#xe67c;</i>上传图片
        </div>
        <?php if(!empty($hint)):?>
        <div class="layui-word-aux" style="display: inline-block;"><?=$hint?></div>
        <?php endif;?>
        <script>
            $(document).ready(function () {
                layui.use('upload', function () {
                    var upload = layui.upload;
                    upload.render({
                        elem: '.bp-qiniu-uploader-<?= $id ?>', //绑定元素
                        multiple: <?= $isMulti ? "true" : "false" ?>,
                        auto: false,
                        acceptMime: <?=$mineTypes?>,
                        choose: function (obj) {
                            var files = obj.pushFile();
                            var token = Bp123GetUpToken("<?=$tokenUrl?>");
                            var putExtra = {
                                mimeType:<?=$mineTypes?>,
                                params:<?=$params?>
                            };
                            var config = {
                                useCdnDomain: <?=$useCdn?>,
                                region: <?=empty($region) ? "null" : $region?>
                            };
                            var observer = {
                                next: function (res) {
                                },
                                error: function (res) {
                                    console.log(res);
                                },
                                complete:function(info){
                                    if(typeof uploadFile<?=$id?> === "function")
                                        uploadFile<?=$id?>(info);
                                    if(typeof <?=$callBack?> === "function"){
                                        <?=$callBack?>(info);
                                    }else
                                        console.log(info);
                                }
                            };
                            $.each(files, function (i, file) {
                                var observable = qiniu.upload(file, null, token, putExtra, config);
                                observable.subscribe(observer);
                                delete files[i]
                            });
                        }
                    })
                });
            });
        </script>