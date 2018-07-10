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
        $data = UserExamQuestion::find()->where(["eid" => $this->id, "uid" => $this->uid])->select("qid,userAnswer,status")->asArray()->all();
        return ArrayHelper::index($data, "qid");
    }

    /**
     * @param int $type
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getQIdsByOffset($type = 0, $offset = 1, $limit = 10) {
        $ids = empty($this->qIds) ? [] : json_decode($this->qIds, true);
        $data = [];
        if (isset($ids[ $type ]) && isset($ids[ $type ][ $offset - 1 ])) {
            $max = min(count($ids[ $type ]), $offset + $limit);
            for (intval($offset); $offset <= $max; $offset++)
                $data[] = (int)$ids[ $type ][ $offset - 1 ];
        }
        if (count($data) < $limit) {
            $_ids = $this->getQIdsByOffset($type + 1, 1, $limit - count($data));
            return array_merge($data, $_ids);
        } else
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
                    "uA"  => isset($answers[ $qid ]) ? $answers[ $qid ]['userAnswer'] : '',
                    "status" => isset($answers[ $qid ]) ? $answers[ $qid ]['status'] : 0,
                ];
            }
        }
        return $data;
    }

    public function Score($type) {
        $qType = QuestionType::findOne($this->tid);
        return $qType->setting($qType->typeEnStr($type) . "Score");
    }
}