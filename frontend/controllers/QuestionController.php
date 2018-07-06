<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/25
 * Time: 下午11:13
 */

namespace frontend\controllers;

use Yii;
use common\models\Question;
use common\models\QuestionType;
use common\models\UserQuestionType;
use common\tools\Status;
use common\tools\Tool;

class QuestionController extends BaseController
{
    public function actionAllTypes() {
        $types = QuestionType::all();
        $user = $this->getUser();
        $value = [0, 0];
        if ($user->tid > 0)
            foreach ($types as $i => $type) {
                if ($user->tid == $type['tid']) {
                    $value[0] = $i;
                    foreach ($type['child'] as $j => $val) {
                        if ($user->tid2 == $val['tid']) {
                            $value[1] = $j;
                            break;
                        }
                    }
                    break;
                }
            }
        return Tool::reJson(["types" => array_values($types), "value" => $value]);
    }

    public function actionChangeType() {
        $tid = $this->getPost("tid");
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);
        $user = $this->getUser();
        if ($user->tid == $type->parentId && $user->tid2 == $tid)
            return Tool::reJson(null, "已在当前分类", Tool::FAIL);
        $expireAt = UserQuestionType::find()->where(["uid" => $user->id, "tid" => $tid])->orderBy("expire_at desc")->select("expire_at")->scalar();
        $user->tid = $type->parentId;
        $user->tid2 = $tid;
        $user->expire_at = intval($expireAt);
        $user->save();
        $types = QuestionType::all();
        $value = [0, 0];
        foreach ($types as $i => $type) {
            if ($user->tid == $type['tid']) {
                $value[0] = $i;
                foreach ($type['child'] as $j => $val) {
                    if ($user->tid2 == $val['tid']) {
                        $value[1] = $j;
                        break;
                    }
                }
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
                "child" => $type->nums()
            ];
        return Tool::reJson([
            "type"  => $typeData,
            "types" => $data
        ]);
    }

    public function actionTrainHistory() {

    }

    public function actionTrainList($tid = 0, $offset = 1, $type = Question::TypeJudge) {
        $qType = QuestionType::findOne($tid);
        if (!$qType || $qType->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);

        $offset = $offset <= 1 ? 1 : $offset;

        $questions = Question::find()->where(["tid" => $tid, "type" => $type])->offset($offset - 1)->limit(10)->all();
        /* @var $questions Question[] */
        $data = [];
        foreach ($questions as $index => $question) {
            $data[ $offset + $index ] = $question->info();
            $question->addViewNum();
        }

        return Tool::reJson(["list" => $data]);
    }

    public function actionAnswer() {
        $qid = $this->getPost("qid", 0);
        $answer = $this->getPost("answer", "");
        $offset = $this->getPost("offset", 0);
        $question = Question::findOne($qid);
        if (!$question)
            return Tool::reJson(null, "未发现题目", Tool::FAIL);
        $result = $question->answer();
        if (!empty($answer)) {
            $answer = asort(str_split($answer));
            if ($answer == $result['answer']) {
                $result['result'] = true;
                $question->addSuccessNum();
            } else {
                $question->addErrNum();
                $result['result'] = false;
            }
        }
        $this->getUser()->updateQuestionRecord($offset);
        return Tool::reJson(["result" => $result]);
    }
}