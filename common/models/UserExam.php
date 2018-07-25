<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/8
 * Time: 上午8:46
 */

namespace common\models;

use yii\helpers\ArrayHelper;


/**
 * @property User $user
 * @property QuestionType $type
 */
class UserExam extends \common\models\base\UserExam
{
    const ExamIng = 0;
    const ExamFinish = 1;
    const ExamExpire = 2;

    public function getType() {
        return $this->hasOne(QuestionType::className(), ["id" => "tid"]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

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
        $detail = empty($this->detail) ? [] : json_decode($this->detail, true);
        return ArrayHelper::merge($detail, [
            "eid"       => $this->id,
            "status"    => $this->status,
            "score"     => $this->score,
            "expire_at" => $this->expire_at,
            "finish_at" => $this->finish_at > 0 ? date("Y-m-d H:i:s", $this->finish_at) : "",
        ]);
    }

    public function finishQuestions() {
        $data = \Yii::$app->db->cache(function () {
            return UserExamQuestion::find()->where(["eid" => $this->id, "uid" => $this->uid])->select("qid,userAnswer,status")->asArray()->all();
        }, 5);
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
                    "qid"    => $qid,
                    "uA"     => isset($answers[ $qid ]) ? $answers[ $qid ]['userAnswer'] : '',
                    "status" => isset($answers[ $qid ]) ? $answers[ $qid ]['status'] : 0,
                ];
            }
        }
        return $data;
    }

    public function Score($type) {
        $qType = $this->type;
        return $qType->setting($qType->typeEnStr($type) . "Score");
    }

    public static function examInfo($uid, $tid) {
        return \Yii::$app->db->cache(function () use ($uid, $tid) {
            return self::find()->where(["uid" => $uid, "tid" => $tid, "status" => self::ExamFinish])->select("count(*) as num,AVG(score) as avg,Max(score) as max")->asArray()->one();
        }, 30);
    }

    public function useTime() {
        if ($this->status == self::ExamFinish)
            $sec = $this->finish_at - $this->created_at;
        else
            $sec = $this->expire_at - $this->created_at;
        return floor($sec / 60) . ":" . $sec % 60;
    }
}