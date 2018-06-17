<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/27
 * Time: 下午4:24
 */

namespace frontend\controllers;


use common\models\Slider;
use common\tools\Tool;

class SliderController extends BaseController
{
    public function actionIndex() {
        return Tool::reJson(["list" => Slider::index()]);
    }
}