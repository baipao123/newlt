<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-16
 * Time: 14:51:08
 */

namespace layuiAdm\widgets;


class TableWidget extends Widget
{
    public $header;

    public $body;

    public function run(){

    }

    public static function begin($config = []) {
        return parent::begin($config);
    }

    public static function end() {
        return parent::end();
    }

}