<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-16
 * Time: 14:51:08
 */

namespace layuiAdm\widgets;


use yii\helpers\ArrayHelper;

class TableWidget extends Widget
{
    public $header = [];

    public $width;

    public $height;

    public $cellMinWidth;

    public $limit = 10;

    public $tableId;

    public $skin = "line";

    public $even = true;

    public $size;

    public $noneText = "暂无相关数据";

    public static function begin($config = []) {
        $html = self::table($config);
        $html .= self::header($config);
        $html .= '<tbody class="layui-table-body">';
        echo $html;
    }

    public static function end() {
        echo '</tbody></table>';
        echo "<script>layui.use('table', function(){ var table=layui.table; table.init('table-" . self::$counter . "')});</script>";
    }

    protected static function table($config) {
        $width = ArrayHelper::getValue($config, "width", 0);
        $height = ArrayHelper::getValue($config, "height", 0);
        $cellMinWidth = ArrayHelper::getValue($config, "cellMinWidth", 0);
        $tableId = ArrayHelper::getValue($config, "tableId", "");
        $skin = ArrayHelper::getValue($config, "skin", "line");
        $even = ArrayHelper::getValue($config, "even", true);
        $size = ArrayHelper::getValue($config, "size", "");
        $noneText = ArrayHelper::getValue($config, "noneText", "暂无相关数据");
        $limit = ArrayHelper::getValue($config, "limit", 10);

        $layData = [
            "skin"  => $skin,
            "even"  => $even,
            "limit" => $limit,
            "text"  => [
                "none" => $noneText
            ]
        ];
        if (!empty($width))
            $layData['width'] = $width;
        if (!empty($height))
            $layData['height'] = $height;
        if (!empty($cellMinWidth))
            $layData['cellMinWidth'] = $cellMinWidth;
        if (!empty($tableId))
            $layData['ID'] = $tableId;
        if (!empty($size))
            $layData['size'] = $size;

        $html = '<table class="layui-table ';
        if (!empty($class))
            $html .= (is_string($class) ? $class : implode(" ", $class));
        $html .= '" ';
        if (!empty($style))
            $html .= 'style="' . (is_string($style) ? $style : implode(";", $style)) . '" ';
        $html .= 'lay-data="' . self::layDataForJs($layData) . '" ';
        $html .= 'lay-filter="table-' . self::$counter . '"';
        $html .= '>';
        return $html;
    }

    protected static function header($config) {
        $header = ArrayHelper::getValue($config, 'header', []);
        if (empty($header))
            return '';
        $html = '<thead><tr>';
        foreach ($header as $i => $val) {
            $html .= self::th($i, $val);
        };
        return $html . '</tr></thead>';

    }

    protected static function th($i, $val) {
        $layData = [
            "field" => "field" . $i
        ];
        if (is_array($val))
            $layData = ArrayHelper::merge($layData, $val);
        $html = '<th lay-data="' . self::layDataForJs($layData) . '">';
        $html .= is_array($val) ? $i : $val;
        return $html . '</th>';
    }

    protected static function layDataForJs($arr) {
        $str = '{';
        foreach ($arr as $key => $value) {
            if (is_array($value))
                $str .= $key . ':' . self::layDataForJs($value);
            else if (is_int($value) || is_bool($value))
                $str .= $key . ':' . json_encode($value);
            else
                $str .= $key . ":'{$value}'";
            $str .= ',';
        }
        return rtrim($str, ',') . '}';
    }
}