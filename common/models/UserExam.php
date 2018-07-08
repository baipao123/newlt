<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/8
 * Time: 上午8:46
 */

namespace common\models;


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
}