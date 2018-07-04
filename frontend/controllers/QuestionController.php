<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/25
 * Time: 下午11:13
 */

namespace frontend\controllers;


use common\models\Question;
use common\models\QuestionType;
use common\models\UserQuestionType;
use common\tools\Status;
use common\tools\Tool;

class QuestionController extends BaseController
{
    public function actionChangeType() {
        $tid = $this->getPost("tid");
        $type = QuestionType::findOne($tid);
        if (!$type || $type->status != Status::PASS)
            return Tool::reJson(null, "不存在的分类", Tool::FAIL);
        $user = $this->getUser();
        if ($user->tid == $tid)
            return Tool::reJson(null, "已在当前分类", Tool::FAIL);
        $expireAt = UserQuestionType::find()->where(["uid" => $user->id, "tid" => $tid])->orderBy("expire_at desc")->select("expire_at")->scalar();
        $user->tid = $tid;
        $user->expire_at = intval($expireAt);
        $user->save();
        return Tool::reJson(["user" => $user->info()]);
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
        }

        return Tool::reJson(["list" => $data]);
    }
}