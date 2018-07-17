<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace layuiAdm\widgets;

use Yii;
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
    public $assetFiles;

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
}
