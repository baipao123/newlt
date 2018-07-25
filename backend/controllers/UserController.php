<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/29
 * Time: 下午7:58
 */

namespace backend\controllers;

use common\models\District;
use common\models\User;
use common\models\UserHasJob;
use common\models\UserJobDaily;
use Yii;
use yii\data\Pagination;

class UserController extends BaseController
{
    public $basicActions = ["info"];

    public function actionList($name = "", $openid = "") {
        $query = User::find();
        if (!empty($openid))
            $query->andWhere(["openId" => $openid]);
        if (!empty($name))
            $query->andWhere(["LIKE", "nickname", $name]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(20);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "created_at" => SORT_ASC
            ])
            ->all();
        return $this->render("list", [
            "records"    => $records,
            "pagination" => $pagination,
            "openid"     => $openid,
            "name"       => $name,
        ]);
    }
}