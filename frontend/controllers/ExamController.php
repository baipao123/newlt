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
            $tmp = Question::find()->where(["tid" => $tid, "parentId" => 0, "status" => Status::PASS])->orderBy("RAND()")->limit($num)->select("id,answer")->asArray()->all();
            $qIds[ $tid ] = $tmp;
            $scores[$tid] = $val['score'];
            $allQIds = array_merge($allQIds, $tmp);
        }
        $cQIds = Question::find()->where(["id" => $allQIds, "type" => Question::TypeMultiQuestion])->select("id")->column();
        $childQIds = Question::find()->where(["parentId" => $cQIds])->select("id,parentId,answer")->asArray()->all();
        $childData = [];
        foreach ($childQIds as $val) {
            $childData[ $val['parentId'] ][] = [
                "id"     => $val['id'],
                "answer" => $val['answer']
            ];
        }
        $total = 0;
        foreach ($qIds as $tid => $qIdArr) {
            foreach ($qIdArr as $val) {
                $qId = $val['id'];
                $answer = $val['answer'];
                if (in_array($qId, $cQIds)) {
                    if (isset($childData[ $qId ])) {
                        $qData[] = [$uid, $tid, $eid, $qId, 0, 0, $answer, $time];
                        foreach ($childData[ $qId ] as $qVal) {
                            $q = $qVal['id'];
                            $a = $qVal['answer'];
                            $qData[] = [$uid, $tid, $eid, $q, $qId, $scores[ $tid ], $a, $time];
                            $total++;
                        }
                    }
                } else {
                    $qData[] = [$uid, $tid, $eid, $qId, 0, $scores[ $tid ], $answer, $time];
                    $total++;
                }
            }
        }
        $exam->num = count($qIds);
        $exam->totalNum = $total;
        $exam->save();
        Yii::$app->db->createCommand()->batchInsert(UserExamQuestion::tableName(),["uid","tid","eid","qid","parentQid","score","answer","created_at"],$qData)->execute();
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
        list($qNum, $max) = $exam->questionIndex();
        return $this->send([
            "exam" => $exam->info(),
            "qNum" => $qNum,
            "num"  => $max,
            "doneNum" => UserExamQuestion::find()->where(["eid"=>$eid,"status"=>UserExamQuestion::Done])->count(),
        ]);
    }

    public function actionList() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        $offset = $this->getPost("offset", 1);
        $examData = UserExamQuestion::find()->where(["eid" => $eid, "parentQid" => 0])->offset($offset - 1)->limit(10)->select("id,qid,userAnswer")->orderBy("id asc")->asArray()->all();
        $qIds = ArrayHelper::getColumn($examData, "qid");
        $answerData = ArrayHelper::map($examData, "qid", "userAnswer");
        $examDataSub = UserExamQuestion::find()->where(["eid" => $eid, "parentQid" => $qIds])->select("id,qid,userAnswer")->orderBy("id asc")->asArray()->all();
        foreach ($examDataSub as $val) {
            $answerData[ $val['qid'] ] = $val['userAnswer'];
        }

        $questions = Question::find()->where(["id" => $qIds])->all();
        /* @var $questions Question[] */
        $questions = ArrayHelper::index($questions, "id");
        $data = [];
        foreach ($examData as $val) {
            $qid = $val['qid'];
            $question = $questions[$qid];
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
        $answer = empty($answer) ? [] : json_decode($answer,true);
        if (empty($answer))
            return $this->sendError("请填写答案");
        $question = Question::findOne($qid);
        if (!$question)
            return $this->sendError("未找到试题");
        $userExamQuestions = UserExamQuestion::find()->where(["eid" => $eid])->andWhere(["OR", "qid" => $qid, "parentQid" => $qid])->all();
        $userExamQuestions = ArrayHelper::index($userExamQuestions, "qid");
        /* @var $userExamQuestions UserExamQuestion[] */
        foreach ($answer as $id => $a) {
            $userQuestion = isset($userExamQuestions[ $id ]) ? $userExamQuestions[ $id ] : null;
            if ($userQuestion) {
                $userQuestion->userAnswer = $a;
                $userQuestion->status = UserExamQuestion::Done;
                $userQuestion->updated_at = time();
                $userQuestion->save();
                if ($userQuestion->answer == $userQuestion->userAnswer)
                    Question::addSuccessNum($id);
                else
                    Question::addErrNum($id);
            }
        }
        if (!isset($answer[ $qid ]))
            UserExamQuestion::isDone($eid, $qid);

        $info = $question->info();
        if (!empty($answer)) {
            $questions = $info['children'];
            array_unshift($questions, $info);
            foreach ($questions as $q) {
                if (empty($q))
                    continue;
                $answerText = ArrayHelper::getValue($answer, $q['qid'],"");
                if($qid == $q['qid'])
                    $info['userAnswer'] = $answerText;
                else if(isset($info['children'][$q['qid']]))
                    $info['children'][$q['qid']]['userAnswer'] = $answerText;
            }
        }
        return $this->send(["question"=>$info]);
    }

    public function actionFinish() {
        $eid = $this->getPost("eid", 0);
        $exam = UserExam::findOne($eid);
        if (!$exam || $exam->uid != $this->user_id())
            return $this->sendError("未找到考卷");
        if ($exam->status == UserExam::ExamFinish)
            return $this->sendError("已经交过卷了");
        Yii::$app->db->createCommand("UPDATE `user_exam_question` SET `status`=IF(`userAnswer`=`answer`,:pass,:forbid) WHERE `eid`=:eid AND `status`=:done;", [":eid" => $eid, ":pass" => UserExamQuestion::Success, ":forbid" => UserExamQuestion::Fail, ":done" => UserExamQuestion::Done])->execute();
        $info = UserExamQuestion::find()->where(["eid" => $eid, "uid" => $this->user_id(), "status" => UserExamQuestion::Success])->select("sum(score) as score,count(*) as passNum")->asArray()->one();
        $failNum = UserExamQuestion::find()->where(["eid" => $eid, "uid" => $this->user_id(), "status" => UserExamQuestion::Fail])->select("count(*)")->scalar();
        $exam->score = intval($info['score']);
        $exam->status = UserExam::ExamFinish;
        $exam->finish_at = time();
        $exam->passNum = intval($info['passNum']);
        $exam->errNum = $failNum;
        $exam->save();

        $failParentQids = UserExamQuestion::find()->where(["eid" => $eid, "status" => UserExamQuestion::Fail])->andWhere([">", "parentQid", 0])->distinct()->select("parentQid")->column();
        $notDoParentQids = UserExamQuestion::find()->where(["eid" => $eid, "status" => UserExamQuestion::NotDo])->andWhere([">", "parentQid", 0])->distinct()->select("parentQid")->column();
        $someSuccessParentQids = UserExamQuestion::find()->where(["eid" => $eid, "status" => UserExamQuestion::Success])->andWhere([">", "parentQid", 0])->distinct()->select("parentQid")->column();
        $successParentQids = array_diff($someSuccessParentQids, $failParentQids, $notDoParentQids);
        if (!empty($successParentQids))
            UserExamQuestion::updateAll(["status" => UserExamQuestion::Success], ["eid" => $eid, "qid" => $successParentQids]);
        $finalFailQids = array_merge($failParentQids,array_intersect($notDoParentQids,$someSuccessParentQids));
        if(!empty($finalFailQids))
            UserExamQuestion::updateAll(["status" => UserExamQuestion::Fail], ["eid" => $eid, "qid" => $finalFailQids]);

        return $this->send([
            "score"=>$exam->score
        ]);
    }
}