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
    /**
     * @param string|string[]
     */
    public $class;

    /**
     * @param string|string[]
     */
    public $style;

    public $header = [];

    public $body;

    public function run() {

    }

    public static function begin($config = []) {
        $html = self::table($config);
        $html .= self::header($config);
        $html .= '<tbody>';
        echo $html;
    }

    public static function end() {
        echo '</tbody></table>';
        echo "<script>layui.use('table', function(){ var table=layui.table; table.render()});</script>";
    }

    protected static function table($config) {
        $class = ArrayHelper::getValue($config, "class", '');
        $style = ArrayHelper::getValue($config, "style", '');
        $html = '<table class="layui-table ';
        if (!empty($class))
            $html .= (is_string($class) ? $class : implode(" ", $class));
        $html .= '" ';
        if (!empty($style))
            $html .= 'style="' . (is_string($style) ? $style : implode(";", $style)) . '" ';
        $html .= "lay-data=\"{skin:'line', even:true, size:'sm'}\" ";
        $html .= '>';
        return $html;
    }

    protected static function header($config) {
        $header = ArrayHelper::getValue($config, 'header', []);
        $fixL = ArrayHelper::getValue($config, 'fixL', []);
        $fixR = ArrayHelper::getValue($config, 'fixR', []);
        if (empty($header))
            return '';
        $html = '<thead><tr>';
        foreach ($header as $i => $val) {
            if (in_array($i, $fixL))
                $html .= "<th lay-data=\"{fixed:'left'}\">";
            else if (in_array($i, $fixR))
                $html .= "<th lay-data=\"{fixed:'right'}\">";
            else
                $html .= '<th>';
            $html .= $val;
            $html .= '</th>';
        }
        return $html . '</tr></thead>';

    }
}