<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/25
 * Time: 下午11:58
 */

namespace frontend\controllers;

use common\models\UserExamQuestion;
use yii;
use common\models\Question;
use common\models\QuestionType;
use common\models\UserExam;
use common\tools\Status;
use yii\helpers\ArrayHelper;

class ExamController extends BaseController
{

    public function actionLast($tid = 0) {
        $user = $this->getUser();
        $expire_at = $user->getTidExpire($tid);
        if ($expire_at <= time())
            return $this->sendError("请先购买此分类");
        if ($tid <= 0)
            $tid = $user->tid2;
        $exam = UserExam::getLastExam($this->user_id(), $tid);
        return $this->send([
            "exam" => $exam ? $exam->simpleInfo() : []
        ]);
    }

    public function actionExam() {
        $tid = $this->getPost("tid", 0);
        $user = $this->getUser();
        $expire_at = $user->getTidExpire($tid);
        if ($expire_at <= time())
            return $this->sendError("请先购买此分类");
        if ($tid <= 0)
            $tid = $user->tid2;
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return $this->sendError("不存在的分类");
        $setting = $type->setting();
        $judge = ArrayHelper::getValue($setting, "judgeNum", 0);
        $select = ArrayHelper::getValue($setting, "selectNum", 0);
        $multi = ArrayHelper::getValue($setting, "multiNum", 0);
        $blank = ArrayHelper::getValue($setting, "blankNum", 0);
        $qIds = [
            Question::TypeJudge  => $judge > 0 ? Question::find()->where(["tid" => $tid, "type" => Question::TypeJudge])->orderBy("RAND()")->limit($judge)->select("id")->column() : [],
            Question::TypeSelect => $select > 0 ? Question::find()->where(["tid" => $tid, "type" => Question::TypeSelect])->orderBy("RAND()")->limit($select)->select("id")->column() : [],
            Question::TypeMulti  => $multi > 0 ? Question::find()->where(["tid" => $tid, "type" => Question::TypeMulti])->orderBy("RAND()")->limit($multi)->select("id")->column() : [],
            Question::TypeBlank  => $blank > 0 ? Question::find()->where(["tid" => $tid, "type" => Question::TypeBlank])->orderBy("RAND()")->limit($blank)->select("id")->column() : []
        ];
        $exam = new UserExam;
        $exam->uid = $user->id;
        $exam->tid = $tid;
        $exam->expire_at = time() + ArrayHelper::getValue($setting, "time", 0) * 60;
        $exam->status = UserExam::ExamIng;
        $exam->qIds = json_encode($qIds);
        $exam->created_at = time();
        if (!$exam->save())
            Yii::warning($exam->errors, "保存UserExam错误");
        return $this->send([
            "eid" => $exam->attributes['id']
        ]);
    }

    public function actionRecords($page = 1, $limit = 10) {
        $exams = UserExam::find()
            ->where(["uid" => $this->user_id()])
            ->andWhere(["<>", "status", UserExam::ExamExpire])
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->orderBy("created_at desc")
            ->all();
        /* @var $exams UserExam[] */
        $data = [];
        foreach ($exams as $exam) {
            $data[] = $exam->info();
        }
        return $this->send(["list" => $data]);
    }

    public function actionInfo() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        return $this->send([
            "exam"    => $exam->info(),
            "qNum" => $exam->qNum(),
        ]);
    }

    public function actionList() {
        $eid = $this->getPost("eid", 0);
        $type = $this->getPost("type", Question::TypeJudge);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        $offset = $this->getPost("offset", 1);
        $qIds = $exam->getQIdsByOffset($type, $offset);
        $questions = Question::find()->where(["id" => $qIds])->orderBy(["type" => SORT_ASC, new \yii\db\Expression('FIELD (`id`,' . implode(',', $qIds) . ')')])->all();
        /* @var $questions Question[] */
        $data = [];
        $tmpType = $type;
        foreach ($questions as $index => $question) {
            if ($tmpType != $question->type) {
                $tmpType = $question->type;
                $offset = 1;
            }
            $data[ $tmpType ][ $offset ] = $question->info();
            $offset++;
        }
        return $this->send(["list" => $data]);
    }

    public function actionAnswer() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        if ($exam->status != UserExam::ExamIng || $exam->expire_at <= time())
            return $this->sendError("考试已结束");
        $qid = $this->getPost("qid", 0);
        $answer = $this->getPost("answer", "");
        if (empty($answer))
            return $this->sendError("请选择答案");
        $answerArr = str_split($answer, 1);
        asort($answerArr);
        $answer = join($answerArr);

        $question = Question::findOne($qid);
        if (!$question)
            return $this->sendError("未找到试题");
        $ids = json_decode($exam->qIds, true);
        if (!isset($ids[ $question->type ]) || !in_array($qid, $ids[ $question->type ]))
            return $this->sendError("考卷中未发现此试题");
        $u = UserExamQuestion::findOne(["eid" => $eid, "qid" => $qid, "uid" => $this->user_id()]);
        if (!$u)
            $u = new UserExamQuestion;
        $u->eid = $eid;
        $u->uid = $this->user_id();
        $u->tid = $question->tid;
        $u->qid = $qid;
        $u->answer = $question->answer;
        $u->userAnswer = $answer;
        $u->status = Status::VERIFY;
        if ($u->isNewRecord)
            $u->created_at = time();
        else
            $u->updated_at = time();
        $u->save();
        return $this->send([
            "user" => [
                "qid"    => $qid,
                "uA"     => $answer,
                "status" => Status::VERIFY
            ]
        ]);
    }

    public function actionFinish() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        if ($exam->status == UserExam::ExamFinish)
            return $this->sendError("已经交过卷了");

    }
}