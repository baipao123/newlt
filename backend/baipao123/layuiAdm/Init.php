<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/15
 * Time: 下午2:22
 */

namespace layuiAdm;

use layuiAdm\actions\ErrorExceptionAction;
use Yii;
use layuiAdm\actions\IndexAction;
use layuiAdm\actions\MenuAction;

class Init
{
    public static function SiteActions($admName = "") {
        return [
            'error' => [
                'class' => ErrorExceptionAction::className(),
            ],
            'index' => [
                'class'     => IndexAction::className(),
                'homeTitle' => $admName
            ],
            'menu'  => [
                'class'  => MenuAction::className(),
                'module' => Yii::$app->controller->module
            ],
        ];
    }

    public static function Layout($action) {
        if (in_array($action->id, Yii::$app->controller->basicActions))
            Yii::$app->controller->layout = "@layuiAdm/views/layouts/basic.php";
        else
            Yii::$app->controller->layout = "@layuiAdm/views/layouts/main.php";
    }

}