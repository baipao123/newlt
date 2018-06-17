<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午2:35
 */

namespace layuiAdm\actions;

use Yii;
use yii\base\Action;
use yii\web\View;

class IndexAction extends Action
{
    /**
     *  @var \backend\models\UserIdentify
     */
    protected $user;

    protected $asseturl;

    public $homeTitle = "layui后台";

    public $loginUrl = "/site/login";

    public $changePwdUrl = "admin/change-pwd";

    public $logOutUrl = "site/logout";

    protected $headFiles = [
        "/layui/css/layui.css",
        "/layui/css/layui.hc.css",
        "/layuicms2.0/css/index.css",
        "/layuicms2.0/css/public.css",
        "/jQuery.3.3.1.min.js",
        "/layui/layui.all.js",
    ];

    public function init() {
        parent::init();
        $this->user = Yii::$app->user->getIdentity();
        $this->asseturl = Yii::$app->assetManager->publish(dirname(__FILE__) . '/../assets')[1];
        foreach ($this->headFiles as $file) {
            $exts = explode(".", $file);
            if (end($exts) == "js")
                Yii::$app->view->registerJsFile($this->asseturl . $file, ['position' => View::POS_HEAD]);
            else if (end($exts) == "css")
                Yii::$app->view->registerCssFile($this->asseturl . $file, ['position' => View::POS_HEAD]);
        }
        Yii::$app->view->title = $this->homeTitle;
        return true;
    }

    public function run() {
        if (Yii::$app->user->isGuest)
            return Yii::$app->controller->redirect("/site/login");

        return Yii::$app->controller->renderFile(dirname(__FILE__) ."/../views/layouts/index.php", [
            "user"        => $this->user,
            "assetUrl"    => $this->asseturl,
            "loginUrl"    => $this->loginUrl,
            "changePwdUrl" => $this->changePwdUrl,
            "logOutUrl"   => $this->logOutUrl,
        ]);
    }
}