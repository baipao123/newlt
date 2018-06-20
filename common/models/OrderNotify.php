<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 17:23:44
 */

namespace common\models;


class OrderNotify extends \common\models\base\OrderNotify
{
    const StatusWaitVerify = 0;
    const StatusVerifyFail = 1;
    const StatusAppidError = 2;
    const StatusOrderNotFound = 3;
    const StatusOrderPriceError = 4;
    const StatusVerifyPass = 10;

}