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
            $tid = $user->tid;
        $exam = UserExam::getLastExam($this->user_id(), $tid);
        return $this->send([
            "exam" => $exam ? $exam->simpleInfo() : []
        ]);
    }

    public function actionExam() {
        $tid = $this->getPost("tid", 0);
        $user = $this->getUser();
        $uid = $user->id;
        $time = time();
        $expire_at = $user->getTidExpire($tid);
        if ($expire_at <= time())
            return $this->sendError("请先购买此分类");
        if ($tid <= 0)
            $tid = $user->tid;
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return $this->sendError("不存在的分类");
        $exam = new UserExam;
        $exam->uid = $user->id;
        $exam->tid = $tid;
        $exam->expire_at = time() + ($type->time ?: 60) * 60;
        $exam->status = UserExam::ExamIng;
        $exam->created_at = time();
        if (!$exam->save()) {
            Yii::warning($exam->errors, "保存UserExam错误");
            return $this->sendError("创建模考试卷失败");
        }
        $eid = $exam->attributes['id'];
        $qData = [];
        $qNumList = QuestionType::getExamNumList($tid);
        $qIds = [];
        $allQIds = [];
        $scores = [];
        foreach ($qNumList as $val) {
            $tid = $val['id'];
            $num = $val['examNum'];
            $tmp = Question::find()->where(["tid" => $tid, "parentId" => 0, "status" => Status::PASS])->orderBy("RAND()")->limit($num)->select("id")->column();
            $qIds[ $tid ] = $tmp;
            $scores[$tid] = $val['score'];
            $allQIds = array_merge($allQIds, $tmp);
        }
        $cQIds = Question::find()->where(["id" => $allQIds, "type" => Question::TypeMultiQuestion])->select("id")->column();
        $childQIds = Question::find()->where(["parentId" => $cQIds])->select("id,parentId")->asArray()->all();
        $childData = [];
        foreach ($childQIds as $val) {
            $childData[ $val['parentId'] ][] = $val['id'];
        }
        $total = 0;
        foreach ($qIds as $tid => $qIdArr) {
            foreach ($qIdArr as $qId) {
                if (in_array($qId, $cQIds)) {
                    if (isset($childData[ $qId ])) {
                        $qData[] = [$uid, $tid, $eid, $qId, 0, 0, $time];
                        foreach ($childData[ $qId ] as $q) {
                            $qData[] = [$uid, $tid, $eid, $q, $qId, $scores[ $tid ], $time];
                            $total++;
                        }
                    }
                } else {
                    $qData[] = [$uid, $tid, $eid, $qId, 0, $scores[ $tid ], $time];
                    $total++;
                }
            }
        }
        $exam->num = count($qIds);
        $exam->totalNum = $total;
        $exam->save();
        Yii::$app->db->createCommand()->batchInsert(UserExamQuestion::tableName(),["uid","tid","eid","qid","parentQid","score","created_at"],$qData)->execute();
        return $this->send([
            "eid" => $eid
        ]);
    }

    public function actionRecords($tid = 0, $page = 1, $limit = 10) {
        $tid = empty($tid) ? $this->getUser()->tid : $tid;
        $info = [];
        if ($page == 1) {
            $type = QuestionType::findOne($tid);
            if (!$type)
                return $this->sendError("未找到分类");
            $info = UserExam::examInfo($this->user_id(), $tid);
            $info['avg'] = (string)number_format($info['avg'], 1);
            $info['icon'] = $type->icon();
            $info['name'] = $type->name;
            $info['total'] = $type->score;
            $info['pass'] = $type->passScore;
        }

        $exams = UserExam::find()
            ->where(["uid" => $this->user_id(), "tid" => $tid])
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
        return $this->send(["list" => $data, "info" => $info]);
    }

    public function actionInfo() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        return $this->send([
            "exam"  => $exam->info(),
            "qNum"  => $exam->qNum(),
            "alNum" => $exam->totalNum,
        ]);
    }

    public function actionList() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        $offset = $this->getPost("offset", 1);
        $examData = UserExamQuestion::find()->where(["eid" => $eid, "parentQid" => 0])->offset($offset - 1)->limit(10)->select("id,qid,userAnswer")->asArray()->all();
        $qIds = ArrayHelper::getColumn($examData, "qid");
        $answerData = ArrayHelper::map($examData, "qid", "userAnswer");
        $examDataSub = UserExamQuestion::find()->where(["eid" => $eid, "parentQid" => $qIds])->select("id,qid,userAnswer")->asArray()->all();
        foreach ($examDataSub as $val) {
            $answerData[ $val['qid'] ] = $val['userAnswer'];
        }

        $questions = Question::find()->where(["id" => $qIds])->all();
        /* @var $questions Question[] */
        $questions = ArrayHelper::index($questions, "id");
        $data = [];
        foreach ($questions as $index => $question) {
            $qid = $question->id;
            $info = $question->info($exam->status == UserExam::ExamFinish);
            $info['userAnswer'] = isset($answerData[ $qid ]) ? $answerData[ $qid ] : "";
            if (!empty($info['children'])) {
                foreach ($info['children'] as $i => $value) {
                    $subQid = $value['qid'];
                    $info['children'][ $i ]['userAnswer'] = isset($answerData[ $subQid ]) ? $answerData[ $subQid ] : "";
                }
            }
            $data[ $offset ] = $info;
            Question::addViewNum($question->id);
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
        $u->score = (string)$exam->Score($question->type);
        if ($u->isNewRecord)
            $u->created_at = time();
        else
            $u->updated_at = time();
        $u->save();
        if ($u->answer == $u->userAnswer)
            $question->addSuccessNum();
        else
            $question->addErrNum();
        return $this->send([
            "user"  => [
                "qid"    => $qid,
                "uA"     => $answer,
                "status" => Status::VERIFY
            ],
            "isNew" => $u->updated_at == 0,
        ]);
    }

    public function actionFinish() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        if ($exam->status == UserExam::ExamFinish)
            return $this->sendError("已经交过卷了");
        Yii::$app->db->createCommand("UPDATE `user_exam_question` SET `status`=IF(`userAnswer`=`answer`,:pass,:forbid) WHERE `eid`=:eid;", [":eid" => $eid, ":pass" => Status::PASS, ":forbid" => Status::FORBID])->execute();
        $info = UserExamQuestion::find()->where(["eid" => $eid, "uid" => $this->user_id(), "status" => Status::PASS])->select("sum(score) as score,count(*) as passNum")->asArray()->one();
        $detail = json_decode($exam->detail, true);
        $detail['failNum'] = UserExamQuestion::find()->where(["eid" => $eid, "uid" => $this->user_id(), "status" => Status::PASS])->select("count(*)")->scalar();
        $exam->score = intval($info['score']);
        $exam->status = UserExam::ExamFinish;
        $exam->finish_at = time();
        $exam->detail = json_encode(ArrayHelper::merge($info, $detail));
        $exam->save();
        return $this->send([
            "exam" => $exam->info(),
        ]);
    }
}