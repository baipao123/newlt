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

class QuestionType extends \common\models\base\QuestionType
{

    public function afterSave($insert, $changedAttributes) {
        if ($insert || isset($changedAttributes['name']) || isset($changedAttributes['status']))
            Yii::$app->cache->delete("Question-Type-All");
    }

    public static function all($refresh = false) {
        $cacheKey = "Question-Type-All";
        if (!$refresh && Yii::$app->cache->exists($cacheKey))
            return Yii::$app->cache->get($cacheKey);
        $types = QuestionType::find()->where(["status" => Status::PASS,"parentId"=>0])->orderBy("tid ASC,sort DESC,id ASC")->all();
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
        $names = ArrayHelper::map($types,"id","name");
        $parentIds = ArrayHelper::getColumn($types,"parentId");
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
    public static function getList($tid = 0,$parentId = 0, $status = Status::PASS) {
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

    public function icon($full = false) {
        return Img::icon($this->icon, $full);
    }

    public function setting($key = "") {
        $setting = empty($this->setting) ? [] : json_decode($this->setting, true);
        return empty($key) ? $setting : ArrayHelper::getValue($setting, $key, 0);
    }

    public function updateSetting($arr) {
        $setting = $this->setting();
        foreach ($arr as $key => $value) {
            $value = number_format($value, 1);
            $setting[ $key ] = intval($value) == $value ? intval($value) : $value;
        }
        $this->setting = json_encode($setting);
        $this->save();
    }

    public function qTypes() {
        $setting = $this->setting();
        $data = [];
        $judge = ArrayHelper::getValue($setting, "judgeTotal", 0);
        $select = ArrayHelper::getValue($setting, "selectTotal", 0);
        $multi = ArrayHelper::getValue($setting, "multiTotal", 0);
        $blank = ArrayHelper::getValue($setting, "blankTotal", 0);
        if ($judge > 0)
            $data[] = [
                "type" => Question::TypeJudge,
                "name" => "判断题({$judge})"
            ];
        if ($select > 0)
            $data[] = [
                "type" => Question::TypeSelect,
                "name" => "单选题({$select})"
            ];
        if ($multi > 0)
            $data[] = [
                "type" => Question::TypeMulti,
                "name" => "多选题({$multi})"
            ];
        if ($blank > 0)
            $data[] = [
                "type" => Question::TypeBlank,
                "name" => "填空题({$blank})"
            ];
        return $data;
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
        $arr = [
            Question::TypeJudge  => "判断题",
            Question::TypeSelect => "单选题",
            Question::TypeMulti  => "多选题",
            Question::TypeBlank  => "填空题",
        ];
        return ArrayHelper::getValue($arr, $type, "");
    }
}