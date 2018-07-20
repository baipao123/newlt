<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace layuiAdm\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

/**
 * \yii\bootstrap\Widget is the base class for all bootstrap widgets.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Widget extends \yii\base\Widget
{
    const FORM_ROW = 1;
    const FORM_COLUMN = 2;

    public static $form_default = self::FORM_ROW;

    public $formType;

    public $defaultClasses = [];

    public $classes;

    public $assetFiles;

    public function beforeRun() {
        if (empty($this->formType))
            $this->formType = self::$form_default;
        return parent::beforeRun();
    }

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init() {
        parent::init();
        if (!empty($this->assetFiles)) {
            $assetUrl = Yii::$app->assetManager->publish(dirname(__FILE__) . '/../assets')[1];
            foreach ($this->assetFiles as $file) {
                $extS = explode(".", $file);
                if (end($extS) == "js")
                    $this->getView()->registerJsFile($assetUrl . $file, ['position' => View::POS_HEAD]);
                else if (end($extS) == "css")
                    $this->getView()->registerCssFile($assetUrl . $file, ['position' => View::POS_HEAD]);
            }
        }
    }

    public static function generateOptions($data) {
        $str = '';
        foreach ($data as $key => $val) {
            if ($val === "") {
                $str .= $key . ' ';
                continue;
            }
            if (is_string($val))
                $val = is_int(strpos($val, "\"")) ? str_replace('"', '\"', $val) : $val;
            else
                $val = json_encode($val);
            $str .= $key . '="' . $val . '" ';
        }
        return ' ' . trim($str);
    }

    protected function getClassStr() {
        return self::generateClassStr($this->defaultClasses, $this->classes);
    }

    public static function generateClassStr($defaultClasses, $classes) {
        return implode(" ", ArrayHelper::merge(self::getClassArr($defaultClasses), self::getClassArr($classes)));
    }

    public static function getClassArr($classes) {
        if (empty($classes))
            return [];
        return is_array($classes) ? $classes : [$classes];
    }

    public static function setDefaultFormType($type){
        self::$form_default = $type;
    }
}
