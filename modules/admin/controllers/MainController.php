<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use Yii;
use yii\helpers\Url;
use app\models\User;
use app\models\LoginForm;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class MainController extends Controller
{
    /**
     * Entry point hub-action (redirect depending on role)
     * @return string
     */
    public function actionIndex()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        if($user->role_id == Constants::ROLE_ADMIN){
            return $this->redirect(Url::to(['/admin/users/index']));
        }elseif($user->role_id == Constants::ROLE_REDACTOR){
            return $this->redirect(Url::to(['/admin/tickets/index']));
        }

        return $this->redirect(Url::to(['/admin/main/new-user']));
    }

    /**
     * Login admin-user
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        $this->view->title = "Вход в админ панель";

        /* @var $identity User */
        $identity = Yii::$app->user->identity;
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/admin/main/index']));
        }
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Url::to(['/admin/main/index']));
        }

        return $this->render('login', compact('model'));
    }

    /**
     * Logout
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::to(['/admin/main/login']));
    }

    /**
     * Show this for new user
     * @return string
     */
    public function actionNewUser()
    {
        $this->layout = 'main-login';
        $this->view->title = "Ваша заявка на рассмотрении";
        return $this->render('new-user', compact('model'));
    }
}
