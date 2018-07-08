<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/8
 * Time: 上午8:46
 */

namespace common\models;


use yii\helpers\ArrayHelper;

class UserExam extends \common\models\base\UserExam
{
    const ExamIng = 0;
    const ExamFinish = 1;
    const ExamExpire = 2;

    /**
     * @param int $uid
     * @param int $tid
     * @return self
     */
    public static function getLastExam($uid, $tid) {
        return static::find()
            ->where(["uid" => $uid, "tid" => $tid, "status" => self::ExamIng])
            ->andWhere([">", "expire_at", time()])
            ->orderBy("expire_at DESC")
            ->one();
    }

    public function simpleInfo() {
        if ($this->status != self::ExamIng || $this->expire_at <= time())
            return $this->info();
        return [
            "eid"       => $this->id,
            "expire_at" => $this->expire_at,
        ];
    }

    public function info() {
        if ($this->status == self::ExamIng && $this->expire_at <= time()) {
            $this->status = self::ExamExpire;
            $this->save();
        }
        return [
            "eid"       => $this->id,
            "status"    => $this->status,
            "score"     => $this->score,
            "expire_at" => $this->expire_at,
            "finish_at" => $this->finish_at,
        ];
    }

    public function finishQuestions() {
        $data = UserExamQuestion::find()->where(["eid" => $this->id])->select("id,userAnswer")->column();
        return ArrayHelper::map($data, "id", "userAnswer");
    }

    /**
     * @param int $offset
     * @return array
     */
    public function getQIdsByOffset($offset = 1) {
        $Ids = empty($this->qIds) ? [] : json_decode($this->qIds, true);
        $Ids = empty($Ids) ? [] : array_merge(array_values($Ids));
        $data = [];
        for (intval($offset); $offset <= count($Ids); $offset++) {
            $data[] = $Ids[ $offset - 1 ];
        }
        return $data;
    }

    public function qNum() {
        $Ids = empty($this->qIds) ? [] : json_decode($this->qIds, true);
        if (empty($Ids))
            return [];
        $answers = $this->finishQuestions();
        $data = [];
        foreach ($Ids as $type => $ids) {
            foreach ($ids as $index => $qid) {
                $data[ $type ][ $index + 1 ] = [
                    "qid" => $qid,
                    "uA"  => isset($answers[ $qid ]) ? $answers[ $qid ] : ''
                ];
            }
        }
        return $data;
    }
}