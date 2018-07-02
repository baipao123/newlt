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
            "attaches" => Img::formatFromJson($this->attaches, "question"),
            "options"  => $this->options(),
        ];
    }

    public function options() {
        $data = [];
        if (!empty($this->a) && !empty($this->aImg))
            $data["A"] = [
                "text" => $this->a,
                "img"  => Img::formatFromJson($this->aImg, "question"),
            ];
        if (!empty($this->b) && !empty($this->bImg))
            $data["B"] = [
                "text" => $this->b,
                "img"  => Img::formatFromJson($this->bImg, "question"),
            ];
        if (!empty($this->c) && !empty($this->cImg))
            $data["C"] = [
                "text" => $this->c,
                "img"  => Img::formatFromJson($this->cImg, "question"),
            ];
        if (!empty($this->d) && !empty($this->dImg))
            $data["D"] = [
                "text" => $this->d,
                "img"  => Img::formatFromJson($this->dImg, "question"),
            ];
        return $data;
    }

}