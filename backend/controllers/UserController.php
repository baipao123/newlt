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

    public function actionList($name = "", $phone = "", $gender = -1, $cid = 0, $aid = 0) {
        $query = User::find()->where(["type" => User::TYPE_USER]);
        if ($cid > 0)
            $query->andWhere(["city_id" => $cid]);
        if ($aid > 0)
            $query->andWhere(["area_id" => $aid]);
        if ($gender >= 0)
            $query->andWhere(["gender" => $gender]);
        if (!empty($name))
            $query->andWhere(["LIKE", "realname", $name]);
        if (!empty($phone))
            $query->andWhere(["LIKE", "phone", $phone]);
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
            "cid"        => $cid,
            "aid"        => $aid,
            "gender"     => $gender,
            "name"       => $name,
            "phone"      => $phone,
            "cities"     => District::cities(),
            "areas"      => empty($cid) ? [] : District::areas($cid),
        ]);
    }
}