<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 16:10:38
 */

namespace common\tools;


class Tool
{
    const NEED_LOGIN  = -1;
    const SUCCESS = 0;
    const FAIL = 1;

    public static function reJson($data = null, $text = "", $code = Tool::SUCCESS) {
        return [
            "code" => (int)$code,
            "msg"  => $text,
            "data" => $data,
        ];
    }

}