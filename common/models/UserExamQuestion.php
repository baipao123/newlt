<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/8
 * Time: 下午4:03
 */

namespace common\models;


class UserExamQuestion extends \common\models\base\UserExamQuestion
{

    const NotDo = 0;
    const Done = 1;
    const Success = 2;
    const Fail = 3;

    public static function isDone($eid, $qid){
        $questions = UserExamQuestion::find()->where(["eid" => $eid])->andWhere(["OR", ["qid" => $qid], ["parentQid" => $qid]])->orderBy("parentQid asc")->all();
        /* @var $questions self[] */
        if (count($questions) > 1) {
            $is_done = true;
            $parent = null;
            foreach ($questions as $question) {
                if ($question->parentQid == 0)
                    $parent = $question;
                else if ($question->status == self::NotDo) {
                    $is_done = false;
                    break;
                } else if (in_array($question->status, [self::Success, self::Fail])) {
                    $is_done = false;
                    break;
                }
            }
            if($is_done) {
                $parent->status = self::Done;
                $parent->save();
            }
        }
    }

}