<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/29
 * Time: 下午10:21
 */

namespace layuiAdm\tools;

use Yii;

class Url
{
    public static function selfLink($data = []) {
        $params = $_GET;
        foreach ($data as $key => $value) {
            $params[ $key ] = $value;
        }
        $params[0] = Yii::$app->request->pathInfo;
        return Yii::$app->urlManager->createUrl($params);
    }

    public static function createLink($path, $data) {
        $params[0] = $path;
        foreach ($data as $key => $value) {
            $params[ $key ] = $value;
        }
        return Yii::$app->urlManager->createUrl($params);
    }
}