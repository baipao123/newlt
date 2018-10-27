<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/8
 * Time: 下午4:03
 */

namespace common\models;


class UserExamQuestion extends \common\models\base\UserExamQuestion
{

    const NotDo = 0;
    const Done = 1;
    const Success = 2;
    const Fail = 3;

}