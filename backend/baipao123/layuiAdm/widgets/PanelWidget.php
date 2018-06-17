<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午7:04
 */

namespace layuiAdm\widgets;


class PanelWidget extends Widget
{
    public function init() {
        $this->assetFiles = [
            "/layuicms2.0/css/panel.css"
        ];
        parent::init();
    }

    /**
     * @var $panels $this->formatPanel()[]
     */
    public $panels = [];

    public $aHref = true;//a标签的url已href的形式存在


    public function run() {
        if (empty($this->panels))
            return "";
        $pHtml = '<div class="layui-row layui-col-space10 panel-box panel-box-' . $this->getId() . '">';
        foreach ($this->panels as $panel) {
            $panel = $this->formatPanel($panel);
            $pHtml .= '<div class="panel layui-col-xs12 layui-col-sm6 layui-col-md4 layui-col-lg2">';
            if ($this->aHref)
                $pHtml .= '<a href="' . $panel['href'] . '" ' . ($panel['_target'] ? 'target="_blank"' : '') . '>';
            else
            $pHtml .= '<a href="javascript:;" data-url="' . $panel['href'] . '" ' . ($panel['_target'] ? 'target="_blank"' : '') . '>';
            $pHtml .= '<div class="panel-icon layui-bg-' . $panel['color'] . '">';
            $pHtml .= IWidget::widget(["icon" => $panel['icon']]);
            $pHtml .= '</div>';
            $pHtml .= '<div class="panel-word">';
            $pHtml .= '<span>' . $panel['title'] . '</span>';
            $pHtml .= '<cite>' . $panel['desc'] . '</cite>';
            $pHtml .= '</div>';
            $pHtml .= '</a>';
            $pHtml .= '</div>';
        }
        $pHtml .= '</div>';
        return $pHtml;
    }

    private function formatPanel($panel = []) {
        return [
            "color"   => isset($panel['color']) && !empty($panel['color']) ? $panel['color'] : "green",
            "title"   => isset($panel['title']) ? $panel['title'] : "",
            "icon"    => isset($panel['icon']) ? $panel['icon'] : "",
            "desc"    => isset($panel['desc']) ? $panel['desc'] : "",
            "href"    => isset($panel['href']) ? $panel['href'] : "",
            "_target" => isset($panel['_target']) ? (bool)$panel['_target'] : false,
        ];
    }
}