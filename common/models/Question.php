<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/1
 * Time: 下午10:57
 */

namespace common\models;


class Question extends \common\models\base\Question
{
    const TypeJudge = 1;
    const TypeSelect = 2;
    const TypeMulti = 3;
    const TypeBlank = 4;

}