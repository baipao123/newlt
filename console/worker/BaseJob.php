<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/20
 * Time: 下午9:52
 */

namespace console\worker;

use Yii;

class BaseJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public function execute($queue) {

    }
}