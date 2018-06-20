<?php
/**
 * Created by
 * User: Administrator
 * Date: 2018-06-20
 * Time: 16:55:08
 */

namespace frontend\controllers;


class GoodsController extends BaseController
{
    public function actionPrices($tid = 0) {

    }

    public function actionOrder() {
        $tid = $this->getPost("tid", 0);

    }
}