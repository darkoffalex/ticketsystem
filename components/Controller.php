<?php

namespace app\components;

use app\helpers\Help;
use app\models\User;
use yii\helpers\Url;
use yii\web\Controller as BaseController;
use yii\base\Module;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use Yii;

class Controller extends BaseController
{
    /**
     * Переопределить конструктор
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        //заголовок страниц
        $this->view->title = "Мой сайт";

        //мета-теги
        $this->view->registerMetaTag(['name' => 'description', 'content' => ""]);
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => ""]);

        //open-graph мета-теги
//        $this->view->registerMetaTag(['property' => 'og:description', 'content' => ""]);
//        $this->view->registerMetaTag(['property' => 'og:url', 'content' => ""]);
//        $this->view->registerMetaTag(['property' => 'og:site_name', 'content' => ""]);
//        $this->view->registerMetaTag(['property' => 'og:title', 'content' => ""]);
//        $this->view->registerMetaTag(['property' => 'og:image', 'content' => ""]);
//        $this->view->registerMetaTag(['property' => 'og:image:width', 'content' => '200']);
//        $this->view->registerMetaTag(['property' => 'og:image:height', 'content' => '200']);

        //временная зона
        date_default_timezone_set('Europe/Moscow');

        //включить внешние ключи (для операций со SQLITE базой)
//        Yii::$app->db->createCommand("PRAGMA foreign_keys = ON")->execute();

        //базовый конструктор
        parent::__construct($id,$module,$config);
    }

    /**
     * Выполнять перед каждым action'ом
     * @param Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $disableCSRF = [
            'bot/hook',
            'site/fb-login'
        ];

        $c = $action->controller->id;
        $a = $action->id;

        if(in_array(($c.'/'.$a),$disableCSRF)){
            $this->enableCsrfValidation = false;
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;

        //Update the last visit time
        if(!empty($user)){
            //online at
            $user->online_at = date('Y-m-d H:i:s', time());

            //generate unique bot key
            if(empty($user->bot_key)){
                $key = Help::rndstr(6,true);
                while(User::find()->where(['bot_key' => $key])->count() > 0){
                    $key = Help::rndstr(6,true);
                }
                $user->bot_key = $key;
            }

            $user->update();
        }

        $this->view->registerMetaTag(['property' => 'og:url', 'content' => Url::current([],true)]);
        $this->view->registerMetaTag(['property' => 'og:image', 'content' => "http://1.ctc.gl/os1.png"]);
        $this->view->registerMetaTag(['property' => 'og:image:width', 'content' => '475']);
        $this->view->registerMetaTag(['property' => 'og:image:height', 'content' => '250']);

        if($action->id == 'complaint'){
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => "Заполните форму жалобы за 30 секунд и наблюдайте за ходом ее рассмотрения прямо в Messenger!"]);
            $this->view->registerMetaTag(['property' => 'og:title', 'content' => "Пожаловаться администрации РА"]);
        }elseif($action->id == 'offer'){
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => "У вас идея или отличное предложение для Единой сети РА? Напишите нам!"]);
            $this->view->registerMetaTag(['property' => 'og:title', 'content' => "Предложение администрации РА"]);
        }elseif($action->id == 'comment'){
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => "Ваши добрые слова дают нам стимул, а замечания - помогают стать лучше!"]);
            $this->view->registerMetaTag(['property' => 'og:title', 'content' => "Оставьте отзыв о Единой сети РА!"]);
        }elseif($action->id == 'question'){
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => "Задайте любой интересующий вопрос администрации Единой сети РА. Ваш вопрос будет переадресован наиболее компетентному сотруднику и он обязательно ответит вам!"]);
            $this->view->registerMetaTag(['property' => 'og:title', 'content' => "Задайте свой вопрос администрации РА!"]);
        }

        return parent::beforeAction($action);
    }
}