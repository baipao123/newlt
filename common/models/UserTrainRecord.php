<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/6
 * Time: 下午8:55
 */

namespace common\models;


class UserTrainRecord extends \common\models\base\UserTrainRecord
{
    public static function lastOffset($uid, $tid) {
        $record = self::findOne(["uid" => $uid, "tid" => $tid]);
        return $record ? (int)$record->offset : 0;
    }
}