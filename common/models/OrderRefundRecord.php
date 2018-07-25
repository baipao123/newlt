<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/24
 * Time: 下午10:12
 */

namespace common\models;

/**
 * @property Admin $admin
 **/
class OrderRefundRecord extends \common\models\base\OrderRefundRecord
{

    public function getAdmin() {
        return $this->hasOne(Admin::className(), ["id" => "admin_id"]);
    }
}