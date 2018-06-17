<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/27
 * Time: 下午3:26
 */

namespace common\models;

use common\tools\Img;
use Yii;

class Slider extends \common\models\base\Slider
{
    const STATUS_ON = 1;
    const STATUS_OFF = 2;
    const STATUS_EXPIRE = 3;
    const STATUS_DEL = 4;

    const TYPE_NONE = 0;
    const TYPE_JOB = 1;
    const TYPE_PAGE = 2;
    const TYPE_LINK = 3;

    public static function index() {
        $sliders = Yii::$app->db->cache(function () {
            return self::find()->where(["status" => self::STATUS_ON])->andWhere(["OR", [">", "end_at", time()], ["end_at" => 0]])->andWhere(["<", "start_at", time()])->orderBy("sort desc,id desc")->limit(5)->all();
        }, 15);
        /* @var $sliders self[] */
        $data = [];
        foreach ($sliders as $s) {
            $info = $s->format(true);
            if (!empty($info))
                $data[] = $info;
        }
        return $data;
    }

    public function format($mustOn = false) {
        if ($this->status == self::STATUS_ON && $this->end_at > 0 && $this->end_at <= time()) {
            $this->status = self::STATUS_EXPIRE;
            $this->save();
            if ($mustOn)
                return [];
        }
        return [
            "id"    => $this->id,
            "cover" => $this->cover(),
            "title" => $this->title,
            "type"  => $this->type,
            "tid"   => $this->tid,
            "link"  => $this->link
        ];
    }

    public function cover($host = false) {
        return Img::format($this->cover, 750, 375, $host);
    }

    public function timeStr() {
        if ($this->start_at == 0 && $this->end_at == 0)
            return "永久有效";
        $start = $this->start_at > 0 ? date("Y-m-d H:i:s", $this->start_at) : "";
        $end = $this->end_at > 0 ? date("Y-m-d H:i:s", $this->end_at) : "永久";
        if (empty($start))
            return $end . " 前";
        return $start . "<br>" . $end;
    }

}