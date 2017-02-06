<?php

namespace app\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\Ticket;
use app\models\User;
use app\models\UserMessage;
use Yii;
use app\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ProfileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Index page
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        return $this->redirect(Url::to(['/profile/my-tickets']));
    }

    /**
     * List all tickets of current user
     * @return string
     */
    public function actionMyTickets()
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        $this->layout = 'profile';

        /* @var $user User */
        $user = Yii::$app->user->identity;

        /* @var $tickets Ticket[] */
        $tickets = Ticket::find()
            ->with(['userMessages','userMessages.author'])
            ->where(['author_id' => $user->id])
            ->orderBy('created_at DESC')->all();

        return $this->render('tickets',compact('tickets','user'));
    }

    /**
     * Ticket
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShowTicket($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        $this->layout = 'profile';

        /* @var $user User */
        $user = Yii::$app->user->identity;

        $ticket = Ticket::find()
            ->with(['userMessages','userMessages.author'])
            ->where(['id' => (int)$id, 'author_id' => $user->id])
            ->one();

        if(empty($ticket)){
            throw new NotFoundHttpException('Заявка не найдена',404);
        }

        return $this->render('show',compact('ticket','user'));
    }

    /**
     * Append some info to message
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionAppendTicket($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        $this->layout = 'profile';

        /* @var $user User */
        $user = Yii::$app->user->identity;

        /* @var $model Ticket */
        $model = Ticket::find()
            ->where(['id' => (int)$id, 'author_id' => $user->id])
            ->one();

        if(empty($model)){
            throw new NotFoundHttpException('Заявка не найдена',404);
        }

        $model->scenario = Ticket::SCENARIO_APPEND;

        //ajax validation
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            if($model->validate()){
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->text.= "<br><br><span style='font-style: italic'>Дополнено {$model->created_at}</span><br>".$model->appended_text;
                $model->appendToLog("Пользователь - ".$model->author->getFullName()." дополнил сообщение");
                if($model->update()){
                    User::sendBotNotifications($model,"Пользователь - ".$model->author->getFullName()." дополнил сообщение");
                }

                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('_append',compact('model'));
    }

    /**
     * Adding messages for ticket
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionCommentTicket($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        $this->layout = 'profile';

        /* @var $user User */
        $user = Yii::$app->user->identity;

        /* @var $ticket Ticket */
        $ticket = Ticket::find()
            ->where(['id' => (int)$id, 'author_id' => $user->id])
            ->one();

        if(empty($ticket)){
            throw new NotFoundHttpException('Заявка не найдена',404);
        }

        $model = new UserMessage();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){

            $model->author_id = Yii::$app->user->id;
            $model->ticket_id = $ticket->id;
            $model->created_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->updated_by_id = Yii::$app->user->id;

            if($model->validate()){
                $waitedForAnswer = $ticket->hasOpenedQuestion();
                $model->save();
                $ticket->appendToLog("Автор заявки добавил новое сообщение");

                if(!empty($ticket->performer)){
                    if(!$waitedForAnswer){
                        $ticket->performer->botSendMessage("Автор заявки №{$ticket->id} добавил новое сообщение");
                    }else{
                        $ticket->performer->botSendMessage("Автор заявки №{$ticket->id} ответил на вопрос поддержки");
                    }
                }

                return $this->redirect(Yii::$app->request->referrer);
            }


        }

        return $this->renderAjax('_add_comment',compact('model','ticket'));
    }

    /**
     * Delete ticket
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDeleteTicket($id)
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;

        $ticket = Ticket::find()
            ->where(['id' => (int)$id, 'author_id' => $user->id])
            ->one();

        if(empty($ticket)){
            throw new NotFoundHttpException('Заявка не найдена',404);
        }

        $ticket->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Show main settings of current user
     * @return string
     */
    public function actionSettings()
    {
        if(Yii::$app->user->isGuest){
            return $this->render('/site/please_login',['type' => 'profile']);
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;

        $this->layout = 'profile';

        $ticketsOpen = Ticket::find()
            ->where(['author_id' => $user->id,'status_id' => [Constants::STATUS_NEW,Constants::STATUS_IN_PROGRESS]])
            ->orderBy('created_at DESC')
            ->all();

        $ticketsClosed = Ticket::find()
            ->where(['author_id' => $user->id,'status_id' => Constants::STATUS_DONE])
            ->orderBy('created_at DESC')
            ->all();

        return $this->render('settings',compact('user','ticketsOpen','ticketsClosed'));
    }
}