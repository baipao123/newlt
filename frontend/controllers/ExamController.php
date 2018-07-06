<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/25
 * Time: 下午11:58
 */

namespace frontend\controllers;


class ExamController extends BaseController
{

    public function actionLast($tid = 0) {
        if ($tid <= 0)
            $tid = $this->getUser()->tid2;

    }

    public function actionExam() {

    }

    public function actionRecords() {

    }


}