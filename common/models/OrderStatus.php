<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/20
 * Time: 下午7:04
 */

namespace common\models;


class OrderStatus extends \common\models\base\OrderStatus
{

    public static function saveRecord($oid, $status) {
        $record = new self;
        $record->oid = $oid;
        $record->status = $status;
        $record->created_at = time();
        return $record->save();
    }
}