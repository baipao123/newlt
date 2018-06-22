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
    <link rel="icon" href="/favicon.ico">
    <?php $this->head() ?>
</head>
<body class="main_body">
<?php $this->beginBody() ?>
<div class="layui-layout layui-layout-admin">
    <!-- 顶部 -->
    <div class="layui-header header">
        <div class="layui-main mag0">
            <a href="#" class="logo">layuiCMS 2.0</a>
            <!-- 显示/隐藏菜单 -->
            <a href="javascript:;" class="seraph hideMenu icon-caidan"></a>
            <!-- 顶级菜单 -->
<!--            <ul class="layui-nav mobileTopLevelMenus" mobile>-->
<!--                <li class="layui-nav-item" data-menu="contentManagement">-->
<!--                    <a href="javascript:;"><i class="seraph icon-caidan"></i><cite>layuiCMS</cite></a>-->
<!--                </li>-->
<!--            </ul>-->
<!--            <ul class="layui-nav topLevelMenus" pc>-->
<!--                <li class="layui-nav-item" data-menu="seraphApi" pc>-->
<!--                    <a href="javascript:;"><i class="layui-icon" data-icon="&#xe705;">&#xe705;</i><cite>使用文档</cite></a>-->
<!--                </li>-->
<!--            </ul>-->
            <!-- 顶部右侧菜单 -->
            <ul class="layui-nav top_menu">
                <li class="layui-nav-item" pc>
                    <a href="javascript:;" class="clearCache"><i class="layui-icon" data-icon="&#xe640;">&#xe640;</i><cite>清除缓存</cite></a>
                </li>
<!--                <li class="layui-nav-item lockcms" pc>-->
<!--                    <a href="javascript:;"><i class="seraph icon-lock"></i><cite>锁屏</cite></a>-->
                <!-- 红点 -->
<!--                <span class="layui-badge-dot"></span>-->
<!--                </li>-->
                <li class="layui-nav-item" id="userInfo">
                    <a href="javascript:;">
                        <?php if(!empty($user->avatar())):?>
                        <img src="<?=$user->avatar()?>" class="layui-nav-img userAvatar" width="35" height="35">
                        <?php endif;?>
                        <cite class="adminName"><?=$user->username?></cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-url="page/user/userInfo.html"><i class="seraph icon-ziliao" data-icon="icon-ziliao"></i><cite>个人资料</cite></a></dd>
                        <dd><a href="javascript:;" onclick="layerOpenIFrame('<?=$changePwdUrl?>','修改密码')"><i class="seraph icon-xiugai" data-icon="icon-xiugai"></i><cite>修改密码</cite></a></dd>
                        <dd><a href="javascript:;" class="logOut"><i class="seraph icon-tuichu"></i><cite>退出</cite></a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <!-- 左侧导航 -->
    <div class="layui-side layui-bg-black">
        <div class="user-photo">
            <?php if(!empty($user->avatar())):?>
            <a class="img" ><img src="<?=$user->avatar()?>" class="userAvatar"></a>
            <?php endif;?>
            <p>你好！<span class="userName"><?=$user->username?></span></p>
            <p class="nowTime"></p>
        </div>
        <div class="navBar layui-side-scroll" id="navBar">
            <ul class="layui-nav layui-nav-tree">

            </ul>
        </div>
    </div>
    <!-- 右侧内容 -->
    <div class="layui-body layui-form">
        <div class="layui-tab mag0 layui-row" lay-filter="bodyTab" id="top_tabs_box">
            <!-- 上方的tab li -->
            <ul class="layui-tab-title top_tab" id="top_tabs">
            </ul>
            <ul class="layui-nav closeBox">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon caozuo">&#xe643;</i> 页面操作</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" class="refresh refreshThis"><i class="layui-icon">&#x1002;</i> 刷新当前</a></dd>
                        <dd><a href="javascript:;" class="closePageOther"><i class="seraph icon-prohibit"></i> 关闭其他</a></dd>
                        <dd><a href="javascript:;" class="closePageAll"><i class="seraph icon-guanbi"></i> 关闭全部</a></dd>
                    </dl>
                </li>
            </ul>
            <!-- 下方的tab iFrame -->
            <div class="layui-tab-content clildFrame">
            </div>
        </div>
    </div>
    <!-- 底部 -->
    <div class="layui-footer footer">
        <p><span>copyright @2018 </span></p>
    </div>
</div>

<!-- 移动导航 -->
<div class="site-tree-mobile"><i class="layui-icon">&#xe602;</i></div>
<div class="site-mobile-shade"></div>


<script>
    var globalLayer,globalBodyTab;
    //值小于10时，在前面补0
    function dateFilter(date) {
        return date < 10 ? "0" + date : date;
    }
    function getLangDate() {
        var dateObj = new Date(), //表示当前系统时间的Date对象
            year = dateObj.getFullYear(), //当前系统时间的完整年份值
            month = dateObj.getMonth() + 1, //当前系统时间的月份值
            date = dateObj.getDate(), //当前系统时间的月份中的日
            day = dateObj.getDay(), //当前系统时间中的星期值
            weeks = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
            week = weeks[day], //根据星期值，从数组中获取对应的星期字符串
            hour = dateObj.getHours(), //当前系统时间的小时值
            minute = dateObj.getMinutes(), //当前系统时间的分钟值
            second = dateObj.getSeconds(), //当前系统时间的秒钟值
            newDate = dateFilter(year) + "年" + dateFilter(month) + "月" + dateFilter(date) + "日 " + " " + dateFilter(hour) + ":" + dateFilter(minute) + ":" + dateFilter(second);
        $(".layui-side .nowTime").html(newDate + "<br>" + week);
        setTimeout("getLangDate()", 1000);
    }
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
            content: url
        });
    }

    function globalOpenIFrame(url, title, icon) {
        globalBodyTab.tabAddiFrame(title, icon, url)
    }


    $(document).ready(function () {
        layui.extend({
            bodyTab: '{/}<?=$assetUrl?>/layuicms2.0/js/bodyTab'
        });

        getLangDate();

        layui.use(["bodyTab","element","layer"],function(){
            layui.element.init();
            var layer = layui.layer,
                tab = layui.bodyTab({
                openTabNum : "10",  //最大可打开窗口数量
                url : "/site/menu" //获取菜单json地址
            });
            globalLayer = layer;
            globalBodyTab = tab;
            getData("");

            function getData(module) {
                $.ajax({
                    type: "get",
                    url: tab.tabConfig.url,
                    data: {module: module},
                    success: function (res) {
                        tab.render(res);
                    }
                });
            }
            //隐藏左侧导航
            $(".hideMenu").click(function(){
                if($(".topLevelMenus li.layui-this a").data("url")){
                    layer.msg("此栏目状态下左侧菜单不可展开");  //主要为了避免左侧显示的内容与顶部菜单不匹配
                    return false;
                }
                $(".layui-layout-admin").toggleClass("showMenu");
            });
            //手机设备的简单适配
            $('.site-tree-mobile').on('click', function(){
                $('body').addClass('site-mobile');
            });
            $('.site-mobile-shade').on('click', function(){
                $('body').removeClass('site-mobile');
            });
            // 添加新窗口
            $(document).on("click",".layui-nav .layui-nav-item a:not('.mobileTopLevelMenus .layui-nav-item a')",function(){
                //如果不存在子级
                if ($(this).siblings().length === 0) {
                    addBodyTab($(this));
                    $('body').removeClass('site-mobile');  //移动端点击菜单关闭菜单层
                } else {
                    let lis = $(this).parent("li").siblings(".layui-nav-itemed");
                    if (lis.length > 2)
                        lis.removeClass("layui-nav-itemed");
                }
            });
            //清除缓存
            $(".clearCache").click(function(){
                window.sessionStorage.clear();
                window.localStorage.clear();
                var index = layer.msg('清除缓存中，请稍候',{icon: 16,time:false,shade:0.8});
                setTimeout(function(){
                    layer.close(index);
                    layer.msg("缓存清除成功！");
                },1000);
            })
            //打开新窗口
            function addBodyTab(_this){
                tab.tabAdd(_this);
            }
            //登出
            $(".top_menu .logOut").click(function () {
                globalLayer.confirm('确定退出登录?', function () {
                    window.location.href = '<?= $logOutUrl ?>';
                });
            });
        });
    });
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>