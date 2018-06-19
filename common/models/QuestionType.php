<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:50
 */

namespace common\models;


use common\tools\Img;
use common\tools\Status;

class QuestionType extends \common\models\base\QuestionType
{

    /**
     * @return  self[]
     */
    public static function getList($tid = 0, $status = Status::PASS) {
        $query = self::find()->where(["parentId" => $tid])->orderBy("sort desc");
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        $types = $query->all();
        return $types;
    }

    public function info() {
        return [
            "tid"    => $this->id,
            "name"   => $this->name,
            "icon"   => $this->icon(),
            "status" => $this->status
        ];
    }

    public function icon($full = false) {
        return Img::icon($this->icon, $full);
    }
}