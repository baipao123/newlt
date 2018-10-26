<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/25
 * Time: 下午11:13
 */

namespace frontend\controllers;

use common\models\UserTrainRecord;
use Yii;
use common\models\Question;
use common\models\QuestionType;
use common\models\UserQuestionType;
use common\tools\Status;
use common\tools\Tool;
use yii\helpers\ArrayHelper;

class QuestionController extends BaseController
{
    public function actionAllTypes() {
        $types = QuestionType::all();
        $user = $this->getUser();
        $value = 0;
        if ($user->tid > 0)
            foreach ($types as $i => $type) {
                if ($user->tid == $type['tid']) {
                    $value = $i;
                    break;
                }
            }
        $type = QuestionType::findOne($user->tid2);
        return Tool::reJson(["types" => array_values($types), "value" => $value]);
    }

    public function actionChangeType() {
        $tid = $this->getPost("tid");
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);
        $user = $this->getUser();
        if ($user->tid == $type->id)
            return Tool::reJson(null, "已在当前分类", Tool::FAIL);
        $expireAt = UserQuestionType::find()->where(["uid" => $user->id, "tid" => $type->tid ?: $type->id])->orderBy("expire_at desc")->select("expire_at")->limit(1)->scalar();
        $user->tid = $type->tid ?: $type->id;
        $user->tid2 = 0;
        $user->expire_at = intval($expireAt);
        $user->save();
        $types = QuestionType::all();
        $value = 0;
        foreach ($types as $i => $t) {
            if ($user->tid == $t['tid']) {
                $value = $i;
                break;
            }
        }
        return Tool::reJson(["user" => $user->info(), "value" => $value]);
    }

    public function actionInfo($tid) {
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);
        $expire = $this->getUser()->getTidExpire($tid);
        $typeData = [
            "on"     => $expire > time(),
            "tid"    => $type->id,
            "name"   => $type->name,
            "expire" => $expire
        ];
        $types = QuestionType::getList($tid);
        $data = [];
        foreach ($types as $type)
            $data[] = [
                "tid"   => $type->id,
                "name"  => $type->name,
                "child" => $type->qTypes()
            ];
        return Tool::reJson([
            "type"  => $typeData,
            "types" => $data
        ]);
    }

    public function actionTrainLastOffset($tid = 0) {
        if (time() > $this->getUser()->getTidExpire($tid))
            return Tool::reJson(null, '请先购买此分类', Tool::FAIL);
        if (empty($tid))
            $tid = $this->getUser()->tid2;
        if (empty($tid))
            return Tool::reJson(null, "请先选择一个分类", Tool::FAIL);
        $offset = UserTrainRecord::lastOffset($this->user_id(), $tid);
        return Tool::reJson([
            "offset" => $offset,
            "tid"    => $tid
        ]);
    }

    public function actionTrainList($tid = 0, $offset = 1) {
        $qType = QuestionType::findOne($tid);
        if (!$qType || $qType->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);

        $offset = $offset <= 1 ? 1 : $offset;

        $questions = Question::find()->where(["tid" => $tid, "status" => Status::PASS, "parentId" => 0])->offset($offset - 1)->limit(10)->all();
        /* @var $questions Question[] */
        $data = [];
        foreach ($questions as $index => $question) {
            $data[ $offset + $index ] = $question->info();
            Question::addViewNum($question->id);
        }

        return Tool::reJson(["list" => $data, "num" => $qType->totalNum]);
    }

    public function actionAnswer() {
        $qid = $this->getPost("qid", 0);
        $answer = $this->getPost("answer", "");
        $answer = empty($answer) ? [] : json_decode($answer,true);
        $offset = $this->getPost("offset", 0);
        $question = Question::findOne($qid);
        if (!$question)
            return Tool::reJson(null, "未发现题目", Tool::FAIL);
        $info = $question->info(true);
        if (!empty($answer)) {
            $questions = $info['children'];
            array_unshift($questions, $info);
            foreach ($questions as $q) {
                if (empty($q))
                    continue;
                $answerText = ArrayHelper::getValue($answer, $q['qid']);
                if($qid == $q['qid'])
                    $info['userAnswer'] = $answerText;
                else
                    $info['children'][$q['qid']]['userAnswer'] = $answerText;
                if ($q['answer']['answer'] == $answerText) {
                    Question::addSuccessNum($q['qid']);
                } else
                    Question::addErrNum($q['qid']);
            }
        }
        $this->getUser()->updateTrainRecord($question->tid, $offset);
        return Tool::reJson(["question" => $info]);
    }
}