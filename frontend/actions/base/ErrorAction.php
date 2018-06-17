<?php
namespace frontend\actions\base;

use common\tools\Tool;

class ErrorAction extends \yii\web\ErrorAction
{
    public function run() {
        return Tool::reJson(null, $this->getExceptionMessage(), Tool::FAIL);
    }

}