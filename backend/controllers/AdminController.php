<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-11
 * Time: 14:41:38
 */

namespace backend\controllers;

use Yii;
use common\models\Admin;
use yii\data\Pagination;

class AdminController extends BaseController
{
    public $enableCsrfValidation = true;

    public $basicActions = ["change-pwd", "status", "create", "reset-pwd"];

    public function beforeAction($action) {
        if (Yii::$app->user->id != 1 && !in_array($action->id, ["change-pwd"])) {
            throw new \yii\base\ErrorException("无此操作权限");
        }
        return parent::beforeAction($action);
    }

    public function actionList($status = "", $username = "") {
        $query = Admin::find()->where(["<>", "status", Admin::STATUS_DELETED]);
        if ($status !== "") {
            $query->andWhere(["status" => $status]);
        }
        if (!empty($username)) {
            $query->andWhere(["like", "username", $username]);
        }
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $admins = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->all();
        return $this->render("list", [
            "status"     => $status,
            "username"   => $username,
            "admins"     => $admins,
            "pagination" => $pagination,
        ]);
    }

    public function actionCreate() {
        $admin = null;
        if (Yii::$app->request->isPost) {
            $pwd = Yii::$app->request->post("aPwd");
            $name = Yii::$app->request->post("aName");
            $admin = new Admin;
            if (Admin::find()->where(["username" => $name])->exists())
                Yii::$app->session->setFlash("warning", "账户名已存在");
            else {
                $admin->username = $name;
                $admin->password = $admin->setPassword($pwd);
                $admin->access = 90;
                $admin->status = Admin::STATUS_ACTIVE;
                $admin->created_at = time();
                if (empty($admin->username) || mb_strlen($admin->username) < 6 || mb_strlen($admin->username) > 12)
                    Yii::$app->session->setFlash("warning", "用户名必须是6-12位");
                elseif (empty($pwd) || strlen($pwd) < 6 || strlen($pwd) > 12)
                    Yii::$app->session->setFlash("warning", "密码必须是6-12位");
                elseif ($admin->save())
                    Yii::$app->session->setFlash("success", "添加管理员账户成功");
                else
                    Yii::$app->session->setFlash("warning", "添加管理员账户失败");
            }
        }
        return $this->render("create", [
            "admin" => $admin,
        ]);
    }
    
    public function actionStatus($aid = 0,$status){
        $admin = Admin::findOne($aid);
        if(!$admin || $admin->status == Admin::STATUS_DELETED)
            Yii::$app->session->setFlash("danger","账户不存在，或已被删除");
        else if($admin->status == $status)
            Yii::$app->session->setFlash("danger","账户状态未变动，无需操作");
        else{
            $admin->status = $status;
            if($status == Admin::STATUS_DELETED)
                $admin->username = $admin->username ."--delete--".$admin->id."--".mt_rand(100,999);
            $admin->save();
            Yii::$app->session->setFlash("success","操作成功");
        }
        return $this->render("../layouts/none");
    }

    public function actionChangePwd() {
        $admin = Admin::findOne(Yii::$app->user->id);
        if (!$admin || $admin->status != Admin::STATUS_ACTIVE) {
            Yii::$app->session->setFlash("danger", "用户不存在或已被禁用");
        } else if (Yii::$app->request->isPost) {
            $oldPwd = Yii::$app->request->post("oldPwd", "");
            $newPwd = Yii::$app->request->post("newPwd", "");
            $newPwd2 = Yii::$app->request->post("newPwd2", "");
            if (!$admin->checkPassword($oldPwd))
                Yii::$app->session->setFlash("warning","原密码错误");
            else if ($newPwd != $newPwd2)
                Yii::$app->session->setFlash("warning","新密码不一致");
            else if ($newPwd == $oldPwd)
                Yii::$app->session->setFlash("warning", "新密码要求与原密码不一致");
            else{
                $admin->password = $admin->setPassword($newPwd);
                $admin->save();
                Yii::$app->session->setFlash("success","修改密码成功");
            }
        }
        return $this->render("change-pass", [
            "admin" => $admin,
        ]);
    }

    public function actionResetPwd($id) {
        if ($id <= 1)
            Yii::$app->session->setFlash("danger", "账户不存在");
        else {
            $admin = Admin::findOne($id);
            if ($id == 1 || !$admin)
                Yii::$app->session->setFlash("danger", "账户不存在");
            else if (Yii::$app->request->isPost) {
                $pwd = Yii::$app->request->post("pwd");
                $password = $admin->setPassword($pwd);
                if (strlen($pwd) < 6 || strlen($pwd) > 12)
                    Yii::$app->session->setFlash("warning", "新密码必须是6-12位");
                else if ($password == $admin->password)
                    Yii::$app->session->setFlash("warning", "新密码不能是原密码");
                else if (($admin->password = $password) && $admin->save()) {
                    Yii::$app->session->setFlash("success", "密码重置成功");
                }
                else {
                    Yii::$app->session->setFlash("warning", "密码重置失败");
                }
            }
        }
        return $this->render("reset-pwd");
    }
}