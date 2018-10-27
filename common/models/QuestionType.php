<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:50
 */

namespace common\models;

use yii;
use common\tools\Img;
use common\tools\Status;
use yii\helpers\ArrayHelper;

/**
 * @property QuestionType $parentType
 **/
class QuestionType extends \common\models\base\QuestionType
{

    public function getParentType() {
        return $this->hasOne(self::className(), ["id" => "parentId"])->alias("q");
    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert || isset($changedAttributes['name']) || isset($changedAttributes['status']))
            Yii::$app->cache->delete("Question-Type-All");
        if ($insert || isset($changedAttributes['status']) || isset($changedAttributes['examNum']) || isset($changedAttributes['tid']) || isset($changedAttributes['score']) || isset($changedAttributes['parentId'])) {
            Yii::$app->cache->delete("TypeExamNumList-" . $this->tid);
            if (isset($changedAttributes['tid']))
                Yii::$app->cache->delete("TypeExamNumList-" . $changedAttributes['tid']);
        }
    }

    public static function all($refresh = false) {
        $cacheKey = "Question-Type-All";
        if (!$refresh && Yii::$app->cache->exists($cacheKey))
            return Yii::$app->cache->get($cacheKey);
        $types = QuestionType::find()->where(["status" => Status::PASS, "parentId" => 0])->orderBy("tid ASC,sort DESC,id ASC")->all();
        /* @var $types self[] */
        $data = [];
        foreach ($types as $type) {
            if ($type->tid == 0) {
                $data[ $type->id ]['tid'] = $type->id;
                $data[ $type->id ]['name'] = $type->name;
            } else
                $data[ $type->tid ]['child'][] = [
                    "tid"  => $type->id,
                    "name" => $type->name
                ];
        }
        $data = array_values($data);
        Yii::$app->cache->set($cacheKey, $data, 3600);
        return $data;
    }

    public static function typesForSelect($is_sub = true) {
        if (!$is_sub) {
            $types = QuestionType::find()->where(["status" => Status::PASS, "tid" => 0])->orderBy("sort DESC,id ASC")->all();
            /* @var $types self[] */
            $data = [];
            foreach ($types as $type) {
                $data[] = [
                    "tid"  => $type->id,
                    "name" => $type->name
                ];
            }
            return $data;
        }
        $types = QuestionType::find()->where(["status" => Status::PASS])->orderBy("tid ASC,parentId ASC,sort DESC,id ASC")->all();
        /* @var $types self[] */
        $data = [];
        $names = ArrayHelper::map($types, "id", "name");
        $parentIds = ArrayHelper::getColumn($types, "parentId");
        foreach ($types as $type) {
            if ($type->tid == 0) {
                $data[ $type->name ] = [];
            } else if ($type->parentId == 0) {
                if (!in_array($type->id, $parentIds))
                    $data[ $names[ $type->tid ] ][] = [
                        "tid"  => $type->id,
                        "name" => $type->name
                    ];
            } else {
                if (!isset($names[ $type->parentId ]))
                    continue;
                $pName = $names[ $type->parentId ];
                $data[ $names[ $type->tid ] ][] = [
                    "tid"  => $type->id,
                    "name" => $pName . '-' . $type->name
                ];
            }
        }
        return $data;
    }


    /**
     * @param int $tid
     * @param int $parentId
     * @param int $status
     * @return QuestionType[]
     */
    public static function getList($tid = 0, $parentId = 0, $status = Status::PASS) {
        $query = self::find()->where(["tid" => $tid, "parentId" => $parentId])->andWhere(["<>", "status", Status::DELETE])->orderBy("sort desc");
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        $types = $query->all();
        return $types;
    }

    public function info() {
        return [
            "tid"    => $this->id,
            "name"   => $this->name,
            "icon"   => $this->icon,
            "status" => $this->status
        ];
    }

    public function title() {
        return [
            "title" => $this->name,
            "desc"  => $this->description
        ];
    }

    public function icon($full = false) {
        return Img::icon($this->icon, $full);
    }

    public function typeEnStr($type) {
        $arr = [
            Question::TypeJudge  => "judge",
            Question::TypeSelect => "select",
            Question::TypeMulti  => "multi",
            Question::TypeBlank  => "blank",
        ];
        return ArrayHelper::getValue($arr, $type, "");
    }

    public function typeCNStr($type) {
        return ArrayHelper::getValue(Question::TypeAll, $type, "");
    }

    public static function getExamNumList($tid) {
        $cacheKey = "TypeExamNumList-" . $tid;
        $data = Yii::$app->cache->get($cacheKey);
        if ($data)
            return $data;

        $types = QuestionType::find()->where(["tid" => $tid, "status" => Status::PASS])->orderBy("`parentId`=0 asc,sort desc,id asc")->all();
        /* @var $types self[] */
        $data = [];
        $childData = [];
        foreach ($types as $type) {
            if ($type->parentId > 0) {
                $childData[ $type->parentId ][] = [
                    "id"      => $type->id,
                    "examNum" => $type->examNum,
                    "score"   => $type->score
                ];
            } else if (isset($childData[ $type->id ])) {
                foreach ($childData[ $type->id ] as $val) {
                    $data[] = $val;
                }
            } else
                $data[] = [
                    "id"      => $type->id,
                    "examNum" => $type->examNum,
                    "score"   => $type->score
                ];
        }
        Yii::$app->cache->set($cacheKey, $data, 3600);
        return $data;
    }
}