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
use yii\helpers\ArrayHelper;

class QuestionType extends \common\models\base\QuestionType
{

    /**
     * @param int $tid
     * @param int $status
     * @return QuestionType[]
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

    public function nums(){
        $setting = $this->setting();
        $data = [];
        $judge = ArrayHelper::getValue($setting,"judgeTotal",0);
        $select = ArrayHelper::getValue($setting,"selectTotal",0);
        $multi = ArrayHelper::getValue($setting,"multiTotal",0);
        $blank = ArrayHelper::getValue($setting,"blankTotal",0);
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
}