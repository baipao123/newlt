<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/20
 * Time: 下午7:59
 */

namespace layuiAdm\widgets\formWidgets;


use yii\web\View;

class TextArea extends InputOptions
{
    public $defaultClasses = ['layui-textarea'];

    public $needValue = false;

    public $needEditor = false;

    public function init() {
        if ($this->needEditor) {
            $this->assetFiles[] = "/umeditor/themes/default/_css/umeditor.css";
            $this->assetFiles[] = "/umeditor/template.min.js";
            $this->assetFiles[] = "/umeditor/umeditor.config.js";
            $this->assetFiles[] = "/umeditor/umeditor.min.js";
            $this->assetFiles[] = "/umeditor/lang/zh-cn/zh-cn.js";

        }
        parent::init();
    }

    public function run() {
        $config = $this->config();
        $js = '';
        if ($this->needEditor) {
            $config['lay-ignore'] = '';
            $js = <<<JS
            $(function() {
                      window.um = UM.getEditor('{$this->id}', {
                      	 autoFloatEnabled:false,
                         zIndex:899,
        	/* 传入配置参数,可配参数列表看umeditor.config.js */
            toolbar: ['undo redo | bold italic underline | forecolor backcolor | justifyleft justifyright justifycenter | formula']
        });
            })
JS;
        }
        $html = '<textarea' . self::generateOptions($config) . '>' . $this->value . '</textarea>';
        if ($js) {
            $html .= "<script>" . $js . "</script>";
        }
        return $html;
    }
}