/*
	@Author: 驊驊龔頾
	@Time: 2017-10
	@Tittle: bodyTab
	@Description: 点击对应按钮添加新窗口
*/

layui.define(["element","jquery"],function(exports){
	var element = layui.element,
		$ = layui.$,
        Tab = function () {
            this.tabConfig = {
                openTabNum: 10,  //最大可打开窗口数量
                tabFilter: "bodyTab",  //添加窗口的filter
                changeRefreshBool: false,//点击之后是否刷新页面
            };
            this.menu = [];
            this.curmenu = {};
            this.curModule = "";
        };

    Tab.prototype.delMenuStorage = function () {
        this.menu = [];
        window.sessionStorage.removeItem("bp-curmenu");
    }
    Tab.prototype.pushCurToMenu = function (t) {
        this.menu.push(t);
    }
    Tab.prototype.setCurMenu = function (t) {
        this.curmenu = t;
        if (typeof t !== "string")
            t = JSON.stringify(t);
        window.sessionStorage.setItem("bp-curmenu", t);
    }
    Tab.prototype.getCurMenu = function () {
        var cur = window.sessionStorage.getItem("bp-curmenu");
        return cur === "" ? {} : JSON.parse(cur);
    }
    

    Tab.prototype.generateIconIHtml = function(icon){
        //layui-tab-i 用于标记本i是图标
        if (icon === undefined || icon === "")
            return "";
        if (icon.indexOf("icon-") !== -1)
            return '<i class="layui-tab-i seraph ' + icon + '" data-icon="' + icon + '"></i>';
        return '<i class="layui-tab-i layui-icon" data-icon="' + icon + '">' + icon + '</i>';
	}

	Tab.prototype.generateAHeadHtml = function(href,target){
        if (target === "_blank")
            return '<a data-url="' + href + '" target="' + target + '">';
        return '<a data-url="' + href + '">';
	}

    //生成左侧菜单
    Tab.prototype.navBar = function(data){
        var that = this;
        var ulHtml = '';
        $.each(data, function (i, val) {
            var liClass = i === 0 ? "layui-this" : "";
            if (val.spread || val.spread === undefined)
                ulHtml += '<li class="layui-nav-item layui-nav-itemed ' + liClass + '">';
            else
                ulHtml += '<li class="layui-nav-item ' + liClass + '">';
            //三级菜单
            if (val.children !== undefined && val.children.length > 0) {
                ulHtml += '<a>';
                ulHtml += that.generateIconIHtml(val.icon);
                ulHtml += '<cite>' + val.title + '</cite>';
                ulHtml += '<span class="layui-nav-more"></span>';
                ulHtml += '</a>';
                ulHtml += '<dl class="layui-nav-child">';
                $.each(val.children, function (j, item) {
                    ulHtml += '<dd>';
                    ulHtml += that.generateAHeadHtml(item.href, item.target);
                    ulHtml += that.generateIconIHtml(item.icon);
                    ulHtml += '<cite>' + item.title + '</cite></a></dd>';
                });
                ulHtml += "</dl>";
            } else {
                ulHtml += that.generateAHeadHtml(val.href, val.target);
                ulHtml += that.generateIconIHtml(val.icon);
                ulHtml += '<cite>' + val.title + '</cite></a>';
            }
            ulHtml += '</li>';
        });
        return ulHtml;
    }
	//获取二级菜单数据
	Tab.prototype.render = function(data) {
		//显示左侧菜单
		var _this = this;
		$(".navBar ul").html(_this.navBar(data)).height($(window).height()-210);
		//生成后台首页
        _this.renderHome(data[0]);
        element.init();  //初始化页面元素
		$(window).resize(function(){
			$(".navBar").height($(window).height()-210);
		})
	}
	Tab.prototype.renderHome = function (home) {
        var that = this,
            homeLi = $(".layui-tab-title.top_tab li").eq(0),
            titleHtml = that.generateIconIHtml(home.icon) + '<cite>' + home.title + '</cite><i data-url="' + home.href + '"></i>';
        //添加home，还是替换home
        if (homeLi.length === 0) {
            element.tabAdd(that.tabConfig.tabFilter, {
                title: titleHtml,
                content: "<iframe class='child-iFrame' src='" + home.href + "' data-id='-1'></frame>",
                id: "home"
            });
            //刷新进入本页 | 就是空的，重新请求了module
            var cur = that.getCurMenu();
            if (cur && cur !== {} && cur.title && cur.href)
                that.tabAddiFrame(cur.title, cur.icon, cur.href);
            else
                element.tabChange(that.tabConfig.tabFilter, "home").init();
        } else {
            homeLi.html(titleHtml);
            $(".clildFrame .layui-tab-item").eq(0).find("iframe").eq(0).attr("src", home.href);
            element.tabChange(that.tabConfig.tabFilter, "home").init();
        }
    }

	//是否点击窗口切换刷新页面
    Tab.prototype.changeRegresh = function (index) {
        if (this.tabConfig.changeRefreshBool)
            $(".clildFrame .layui-tab-item").eq(index).find("iframe")[0].contentWindow.location.reload();
    }

	//参数设置
	Tab.prototype.set = function(option) {
		var _this = this;
		$.extend(true, _this.tabConfig, option);
		return _this;
	};

    Tab.prototype.getLayId = function (url) {
        var layId = 0;
        $(".layui-tab-title.top_tab li").each(function (i) {
            if ($(this).find("i").eq(-1).attr("data-url") === url)
                layId = $(this).attr("lay-id");
        });
        return layId;
    };
    Tab.prototype.hasTab = function (url) {
        return this.getLayId(url) !== 0;
    };
    Tab.prototype.getTabIndex = function (url) {
        var index = 0;
        $(".layui-tab-title.top_tab li").each(function (i) {
            if ($(this).find("i").eq(-1).attr("data-url") === url)
                index = i;
        });
        return index;
    };

    //右侧内容tab操作
    var tabIdIndex = 0;
    Tab.prototype.tabAdd = function (_this) {
        var that = this;
        if (_this.attr("target") === "_blank") {
            window.open(_this.attr("data-url"));
        } else if (_this.attr("data-url") !== undefined) {
            var icon = _this.find("i.layui-tab-i").length > 0 ? _this.find("i.layui-tab-i").attr("data-icon") : "";
            that.tabAddiFrame(_this.find("cite").text(), icon, _this.attr("data-url"));
            // that.changeRegresh(_this.parent('.layui-nav-item').index());
        }
    }

    Tab.prototype.tabAddiFrame = function (title, icon, url) {
        var openTabNum = this.tabConfig.openTabNum,
            curmenu = "",
            that = this,
            tabFilter = that.tabConfig.tabFilter;
        if ($(".layui-tab-title.top_tab li").length >= openTabNum) {
            layer.msg('只能同时打开' + openTabNum + '个选项卡哦。不然系统会卡的！');
            return;
        }
        if (that.hasTab(url)) {
            that.changeRegresh(that.getTabIndex(url));//是否需要刷新页面
            element.tabChange(tabFilter, that.getLayId(url)).init();
        } else {
            tabIdIndex++;
            var titleHtml = that.generateIconIHtml(icon);
            titleHtml += '<cite>' + title + '</cite>';
            titleHtml += '<i class="layui-icon layui-unselect layui-tab-close" data-url="' + url.replace('"', '') + '" data-id="' + tabIdIndex + '">&#x1006;</i>';
            element.tabAdd(tabFilter, {
                title: titleHtml,
                content: "<iframe class='child-iFrame' src='" + url + "' data-id='" + tabIdIndex + "'></frame>",
                id: new Date().getTime()
            });
            //当前窗口内容
            curmenu = {
                "icon": icon,
                "title": title,
                "href": url,
                "layId": new Date().getTime()
            };
            that.setCurMenu(curmenu);
            that.pushCurToMenu(curmenu);
            element.tabChange(tabFilter, that.getLayId(url)).init();
        }
    }

    //切换后获取当前窗口的内容
    $(document).on("click", ".top_tab li", function () {
        var i = $(this).index();
        bodyTab.setCurMenu(i > 0 && bodyTab.menu[i - 1] ? bodyTab.menu[i - 1] : "");
        element.tabChange(bodyTab.tabConfig.tabFilter, $(this).attr("lay-id")).init();
        bodyTab.changeRegresh(i);
    });

	//删除tab
	$(document).on("click",".top_tab li i.layui-tab-close",function(e){
	    //阻止冒泡
        // 如果传入了事件对象，那么就是非ie浏览器,它支持W3C的stopPropagation()方法
        if (e && e.stopPropagation)
            e.stopPropagation();
        else
            window.event.cancelBubble = true;//我们使用ie的方法来取消事件冒泡
        //删除tab后重置session中的menu和curmenu
		var tabLi = $(this).parent("li"),
		    layId = tabLi.attr("lay-id"),
		    liIndex = tabLi.index();
        if (bodyTab.menu !== [] && bodyTab.menu[liIndex - 1])
            bodyTab.menu.splice((liIndex - 1), 1);
		//删除当前
        if (tabLi.hasClass("layui-this"))
            bodyTab.setCurMenu(bodyTab.menu[liIndex] ? bodyTab.menu[liIndex] : (bodyTab.menu[liIndex - 2] ? bodyTab.menu[liIndex - 2] : ""));
        element.tabDelete(bodyTab.tabConfig.tabFilter,layId).init();
	})

	//刷新当前
    $(".refresh").on("click", function () {  //此处添加禁止连续点击刷新一是为了降低服务器压力，另外一个就是为了防止超快点击造成chrome本身的一些js文件的报错(不过貌似这个问题还是存在，不过概率小了很多)
        if ($(this).hasClass("refreshThis")) {
            $(this).removeClass("refreshThis");
            $(".clildFrame .layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload();
            setTimeout(function () {
                $(".refresh").addClass("refreshThis");
            }, 2000)
        } else {
            layer.msg("您点击的速度超过了服务器的响应速度，还是等两秒再刷新吧！");
        }
    })

    //关闭其他
    $(".closePageOther").on("click", function () {
        bodyTab.menu = [];
        $(".top_tab li").each(function (i) {
            if (i > 0) {
                if ($(this).hasClass("layui-this"))
                    bodyTab.menu.push(bodyTab.curmenu);
                else {
                    element.tabDelete(bodyTab.tabConfig.tabFilter, $(this).attr("lay-id")).init();
                }
            }
        });
        //渲染顶部窗口
        // tab.tabMove();
    });
    //关闭全部
    $(".closePageAll").on("click", function () {
        bodyTab.menu = [];
        bodyTab.setCurMenu("");
        $(".top_tab li").each(function (i) {
            if (i > 0)
                element.tabDelete(bodyTab.tabConfig.tabFilter, $(this).attr("lay-id")).init();

        });
        //渲染顶部窗口
        // tab.tabMove();
    })

	var bodyTab = new Tab();
	exports("bodyTab",function(option){
		return bodyTab.set(option);
	});
})
