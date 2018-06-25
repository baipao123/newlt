<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:53
 */

namespace common\tools;


class Status
{
    // 审核、列表相关
    const VERIFY = 1;
    const PASS = 2;
    const FORBID = 3;
    const EXPIRE = 4;
    const DELETE = 10;


    // 支付相关
    const WAIT_PAY = 1;
    const IS_UNIFY_ORDER = 10;
    const WAIT_NOTIFY = 11;
    const IS_PAY = 20;
    const IS_REFUND = 30;
    const CANCEL_PAY = 101;

}