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
 * @property UserExamQuestion[] $questions
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

    public function getQuestions() {
        return $this->hasMany(UserExamQuestion::className(), ["eid" => "id"])->andWhere(["parentQid" => 0])->orderBy("id asc");
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
        return [
            "eid"       => $this->id,
            "status"    => $this->status,
            "score"     => $this->score,
            "expire_at" => $this->expire_at,
            "finish_at" => $this->finish_at > 0 ? date("Y-m-d H:i:s", $this->finish_at) : "",
            "totalNum"  => $this->totalNum,
            "passNum"   => $this->passNum,
            "errNum"    => $this->errNum,
        ];
    }

    public function finishQuestions() {
        $data = \Yii::$app->db->cache(function () {
            return UserExamQuestion::find()->where(["eid" => $this->id, "uid" => $this->uid])->select("qid,userAnswer,status")->asArray()->all();
        }, 5);
        return ArrayHelper::index($data, "qid");
    }

    public function questionIndex() {
        $questions = $this->questions;
        $data = [];
        $tids = ArrayHelper::getColumn($questions, "tid");
        $tids = array_unique($tids);
        $types = QuestionType::find()->where(["t.id" => $tids])->joinWith("parentType q")->alias("t")->all();
        /* @var $types QuestionType[]*/
        $types = ArrayHelper::index($types,"id");
        $offset = 1;
        foreach ($questions as $question) {
            if ($question->parentQid == 0 && isset($types[ $question->tid ])) {
                $type = $types[ $question->tid ];
                if ($type->parentId > 0)
                    $type = $type->parentType;
                if (!isset($data[ $type->id ])) {
                    $data[ $type->id ] = [
                        "tid"       => $type->id,
                        "name"      => $type->name,
                        "desc"      => $type->description,
                        "questions" => []
                    ];
                }
                $data[ $type->id ]["questions"][] = [
                    "offset" => $offset,
                    "qid"    => $question->qid,
                    "status" => $question->status,
                ];
                $offset++;
            }
        }
        return [$data, $offset - 1];
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