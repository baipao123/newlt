<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午7:10
 */

namespace layuiAdm\widgets;


class IWidget extends Widget
{
    public $icon;
    /**
     * @var $class string|string[]
     */
    public $className;

    private $text = "";

    public function run() {
        if (empty($this->icon))
            return "";
        $classes = is_string($this->className) ? explode(" ", $this->className) : $this->className;
        $iconArr = explode("-", $this->icon);
        if ($iconArr[0] == "icon") {
            //icon-XX  layuicms2.0
            $classes[] = $this->icon;
            $classes[] = "seraph";
        } else if ($iconArr[0] == "my" && $iconArr[1] == "icon") {
            //my-icon
            $classes[] = $this->icon;
            $classes[] = "my-icon";
        } else {
            $this->text = $this->icon;
        }
        return '<i class="' . implode(" ", $classes) . '">' . $this->text . '</i>';
    }
}