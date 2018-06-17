<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-11
 * Time: 11:03:33
 */

namespace common\models;


use common\tools\Img;

class Admin extends \common\models\base\Admin
{
    const STATUS_DELETED = 0;
    const STATUS_DISABLE = 1;
    const STATUS_ACTIVE = 10;

    public function checkPassword($password) {
        return $this->setPassword($password) === $this->password;
    }

    public function setPassword($password) {
        return crypt($password, substr(md5($this->username . $password), 6));
    }

    public function avatar() {
        return Img::format("youzhun.jpeg", 100, 100, true);
    }
}