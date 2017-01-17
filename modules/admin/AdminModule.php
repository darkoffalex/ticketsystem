<?php

namespace app\modules\admin;

use app\helpers\Constants;
use app\models\User;
use Yii;
use yii\helpers\Url;

/**
 * admin module definition class
 */
class AdminModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->layoutPath = "@app/modules/admin/views/layouts";
        $this->viewPath = "@app/modules/admin/views";
        $this->layout = 'main';
    }

    /**
     * Выполняется перед каждым action'ом
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if(!parent::beforeAction($action)){
            return false;
        }



        if(Yii::$app->user->isGuest && $action->id != 'login'){
            Yii::$app->response->redirect(Url::to(['/admin/main/login']));
            return false;
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;
        if($user->role_id == Constants::ROLE_NEW && ($action->id != 'new-user' && $action->id != 'logout')){
            Yii::$app->response->redirect(Url::to(['/admin/main/new-user']));
            return false;
        }elseif($user->role_id == Constants::ROLE_REDACTOR && $action->controller->id == 'users'){
            Yii::$app->response->redirect(Url::to(['/admin/tickets/index']));
            return false;
        }

        return true;
    }
}
