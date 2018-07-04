<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/7/1
 * Time: 下午10:57
 */

namespace common\models;


use common\tools\Img;

class Question extends \common\models\base\Question
{
    const TypeJudge = 1;
    const TypeSelect = 2;
    const TypeMulti = 3;
    const TypeBlank = 4;

    public function info() {
        return [
            "qid"      => $this->id,
            "type"     => $this->type,
            "title"    => $this->title,
            "attaches" => Img::formatFromJson($this->attaches),
            "options"  => $this->options(),
        ];
    }

    public function options() {
        if($this->type == self::TypeJudge)
            return [
                [
                    "option" => "A",
                    "text"   => "对",
                    "img"    => [],
                ],
                [
                    "option" => "B",
                    "text"   => "错",
                    "img"    => []
                ]
            ];
        $data = [];
        if (!empty($this->a) || !empty($this->aImg))
            $data[] = [
                "option" => "A",
                "text" => $this->a,
                "img"  => $this->aImg,
            ];
        if (!empty($this->b) || !empty($this->bImg))
            $data[] = [
                "option" => "B",
                "text" => $this->b,
                "img"  => $this->bImg,
            ];
        if (!empty($this->c) || !empty($this->cImg))
            $data[] = [
                "option" => "C",
                "text" => $this->c,
                "img"  => $this->cImg,
            ];
        if (!empty($this->d) || !empty($this->dImg))
            $data[] = [
                "option" => "D",
                "text" => $this->d,
                "img"  => $this->dImg,
            ];
        return $data;
    }

    public function answer() {
        return [
            "answer"      => str_split($this->answer),
            "description" => $this->description,
            "knowledge"   => $this->knowledge,
            "difficulty"  => $this->difficulty
        ];
    }

    public function addErrNum() {

    }

    public function addSuccessNum() {

    }

    public function addViewNum() {

    }
}