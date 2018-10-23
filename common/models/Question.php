<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/1
 * Time: 下午10:57
 */

namespace common\models;


use common\tools\Img;
use common\tools\Status;

/**
 * @property QuestionType questionType
 */
class Question extends \common\models\base\Question
{
    const TypeJudge = 1;
    const TypeSelect = 2;
    const TypeMulti = 3;
    const TypeBlank = 4;
    const TypeMultiQuestion = 5;

    const TypeAll = [
        Question::TypeJudge  => "判断题",
        Question::TypeSelect => "单选题",
        Question::TypeMulti  => "多选题",
        Question::TypeBlank  => "填空题",
//        Question::TypeMultiQuestion => "多个答案的题目"
    ];

    public function afterSave($insert, $changedAttributes) {
        if ($this->parentId == 0 && ($insert || isset($changedAttributes['status']))) {
            $type = $this->questionType;
            if ($type) {
                $type->totalNum = Question::find()->where(["tid" => $this->tid, "parentId" => 0, "status" => Status::PASS])->count();
                $type->save();
            }
        }
    }

    public function getQuestionType() {
        return $this->hasOne(QuestionType::className(), ["id" => "tid"]);
    }

    public function info($hasAnswer = false, $emptyOptionNum = 0) {
        $options = $this->options($emptyOptionNum);
        $data = [
            "qid"        => $this->id,
            "type"       => $this->type,
            "title"      => $this->title,
            "attaches"   => Img::formatFromJson($this->attaches),
            "options"    => $options,
            "children"   => $this->children($hasAnswer,count($options)),
            "userAnswer" => ""
        ];
        if($hasAnswer)
            $data['answer'] = $this->answer();
        return $data;
    }

    public function children($hasAnswer = false, $emptyOptionNum = 0) {
        if ($this->type != self::TypeMultiQuestion)
            return [];
        if ($this->parentId != 0)
            return [];
        $questions = Question::find()->where(["parentId" => $this->id, "status" => Status::PASS])->all();
        /* @var $questions Question[] */
        $data = [];
        foreach ($questions as $question) {
            $data[ $question->id ] = $question->info($hasAnswer, $emptyOptionNum);
        }
        return $data;
    }

    public function options($emptyOptionNum = 0) {
        if ($this->type == self::TypeJudge)
            return [
                [
                    "option" => "A",
                    "text"   => "对",
                    "img"    => "",
                ],
                [
                    "option" => "B",
                    "text"   => "错",
                    "img"    => ""
                ]
            ];
        if($this->type == self::TypeBlank)
            return [];
        $data = [];
        if (!empty($this->a) || !empty($this->aImg))
            $data[] = [
                "option" => "A",
                "text"   => ltrim($this->a, "A."),
                "img"    => $this->aImg,
            ];
        if (!empty($this->b) || !empty($this->bImg))
            $data[] = [
                "option" => "B",
                "text"   => ltrim($this->b, "B."),
                "img"    => $this->bImg,
            ];
        if (!empty($this->c) || !empty($this->cImg))
            $data[] = [
                "option" => "C",
                "text"   => ltrim($this->c, "C."),
                "img"    => $this->cImg,
            ];
        if (!empty($this->d) || !empty($this->dImg))
            $data[] = [
                "option" => "D",
                "text"   => ltrim($this->d, "D."),
                "img"    => $this->dImg,
            ];
        if (!empty($this->e) || !empty($this->eImg))
            $data[] = [
                "option" => "E",
                "text"   => ltrim($this->e, "E."),
                "img"    => $this->eImg,
            ];
        if (count($data) < $emptyOptionNum) {
            $arr = ["A", "B", "C", "D", "E"];
            for ($i = count($data); $i < $emptyOptionNum; $i++) {
                $data[] = [
                    "option" => $arr[ $i ],
                    "text"   => "",
                    "img"    => ""
                ];
            }
        }
        return $data;
    }

    public function answer() {
        return [
            "answer"      => $this->answer,
            "description" => $this->description,
            "knowledge"   => $this->knowledge,
            "difficulty"  => $this->difficulty
        ];
    }

    public function addErrNum() {
        \Yii::$app->db->createCommand("UPDATE `question` SET `fail_num`=`fail_num`+1 WHERE `id`=:id", [":id" => $this->id])->execute();
    }

    public function addSuccessNum() {
        \Yii::$app->db->createCommand("UPDATE `question` SET `success_num`=`success_num`+1 WHERE `id`=:id", [":id" => $this->id])->execute();
    }

    public function addViewNum() {
        \Yii::$app->db->createCommand("UPDATE `question` SET `view_num`=`view_num`+1 WHERE `id`=:id", [":id" => $this->id])->execute();
    }

    public static function getIds($tid, $type, $limit) {
        return Question::find()->where(["tid" => $tid, "type" => $type, "status" => Status::PASS])->orderBy("RAND()")->limit($limit)->select("id")->column();
    }

    public function attaches() {
        return Img::formatFromJson($this->attaches);
    }

    public function typesForAdm() {
        $data = [];
        foreach (self::TypeAll as $key => $value) {
            $data[] = [
                "value"    => $key,
                "title"    => $value,
                "disabled" => !$this->isNewRecord
            ];
        }
        return $data;
    }
}