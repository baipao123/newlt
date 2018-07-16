<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/6/19
 * Time: 下午10:50
 */

namespace common\models;


use common\tools\Img;
use common\tools\Status;

/**
 * @property QuestionType $questionType
 */
class QuestionPrice extends \common\models\base\QuestionPrice
{
    const Type_Day = 1;
    const Type_Hour = 2;

    public function getQuestionType() {
        return $this->hasOne(QuestionType::className(), ["id" => "tid"]);
    }

    /**
     * @param int $tid
     * @param int $status
     * @return QuestionPrice[]
     */
    public static function prices($tid = 0, $status = Status::PASS) {
        $query = self::find();
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        if ($tid > 0)
            $query->andWhere(["tid" => $tid]);
        $prices = $query->orderBy("tid ASC,sort DESC")->all();
        return $prices;
    }

    public function title() {
        $type = $this->questionType;
        return $type->name . "-" . $this->hourStr();
    }

    public function info() {
        return [
            "pid"      => $this->id,
            "tid"      => $this->tid,
            "title"    => $this->title(),
            "cover"    => $this->cover(),
            "note"     => $this->note,
            "start_at" => $this->start_at,
            "end_at"   => $this->end_at,
            "hourStr"  => $this->hourStr(),
            "price"    => $this->price,
            "oldPrice" => $this->oldPrice,
            "timeStr"  => $this->timeStrForApi(),// 在这个里面判断 status是否过期
            "status"   => $this->status,
        ];

    }

    public function cover($full = true) {
        if ($this->isNewRecord)
            return "";
        if (!empty($this->cover))
            return Img::icon($this->cover, $full);
        $type = $this->questionType;
        return $type ? Img::icon($type->icon, $full) : "";
    }

    public function hourStr() {
        if ($this->type == self::Type_Day)
            return round($this->hour / 24) . "天";
        return $this->hour . "小时";
    }

    public function timeStr() {
        $str = "";
        if ($this->start_at > 0)
            $str = date("Y-m-d H:i", $this->start_at);
        if ($this->end_at > 0)
            $str .= " - " . date("Y-m-d H:i", $this->end_at);
        else
            $str .= " - 永久";
        return trim($str, " -");
    }

    public function timeStrForApi() {
        if ($this->end_at > 0 && $this->end_at <= time()) {
            if ($this->status == Status::PASS) {
                $this->status = Status::EXPIRE;
                $this->save();
            }
            return "已停售";
        }
        $str = "";
        if ($this->start_at > 0 && $this->start_at > time()) {
            $formatStr = date("Y") == date("Y", $this->start_at) ? "m-d H:i" : "Y-m-d H:i";
            $str .= date($formatStr, $this->start_at) . " - ";
        }
        if ($this->end_at > 0 && $this->end_at > time()) {
            $formatStr = date("Y") == date("Y", $this->end_at) ? "m-d H:i" : "Y-m-d H:i";
            $str .= date($formatStr, $this->end_at) . (empty($str) ? "截止" : "");
        }
        return $str;
    }

}