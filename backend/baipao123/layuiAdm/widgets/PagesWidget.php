<?php

namespace layuiAdm\widgets;

use yii\data\Pagination;

class PagesWidget extends Widget
{
    /**
     * @var Pagination the pagination object that this pager is associated with.
     * You must set this property in order to make LinkPager work.
     */
    public $pagination;

    /**
     * Renders the widget.
     */
    public function run() {
        $pageCount = $this->pagination->pageSize;
        $currPage = $this->pagination->page + 1;
        $totalCount = $this->pagination->totalCount;
        if ($totalCount <= $pageCount)
            return "";
        $layout = ['count', 'prev', 'page', 'next'];
        if($totalCount > 10 * $pageCount)
            $layout[] = 'skip';
        $layoutStr = json_encode($layout);
        $js = <<<JS
        layui.use(['laypage'], function(){
            layui.laypage.render({
                elem: 'page-{$this->getId()}'
                ,count: {$totalCount}
                ,limit: {$pageCount}
                ,layout: {$layoutStr}
                ,curr:{$currPage}
                ,jump: function(obj,first){
                    if(!first){
                        var queryParams = {};
                        var queryString = location.search.replace('?','');
                        queryString = decodeURI(queryString);
                        if(queryString){
                            queryString.split('&').forEach(function(i){
                                var j = i.split('=');
                                queryParams[j[0]]=j[1];
                            });
                        }
                        queryParams['page'] = obj.curr;
                        location.href = location.pathname + '?' + $.param(queryParams) + location.hash;
                        
                    }
                }
            });
        });
JS;
        $this->getView()->registerJs($js);
        return '<div id="page-' . $this->getId() . '"></div>';
    }
}
