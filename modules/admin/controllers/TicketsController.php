<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use app\models\Ticket;
use app\models\TicketComment;
use app\models\TicketSearch;
use app\models\User;
use app\models\UserMessage;
use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\swiftmailer\Message;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


class TicketsController extends Controller
{
    public function actionIndex($id = null)
    {
        $searchModel = new TicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);
        return $this->render('index', compact('searchModel','dataProvider','id'));
    }

    /**
     * Modal for full text
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFullText($id = null)
    {
        $model = Ticket::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Not found', 404);
        }

        $title = 'Полный текст';
        $content = $model->text;
        return $this->getFastModal($title,$content);
    }

    /**
     * Modal for full log
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFullLog($id = null)
    {
        $model = Ticket::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Not found', 404);
        }

        $title = 'Полный текст';
        $content = $model->getLogAsString("<br>");
        return $this->getFastModal($title,$content);
    }

    /**
     * Take to the processing
     * @param null $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionTake($id = null)
    {
        $model = Ticket::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Not found', 404);
        }

        /* @var $user User */
        $user = User::findOne(['id' => Yii::$app->user->id]);

        $model->status_id = Constants::STATUS_IN_PROGRESS;
        $model->performer_id = Yii::$app->user->id;
        $model->updated_by_id = Yii::$app->user->id;
        $model->updated_at = date('Y-m-d H:i:s',time());
        $model->appendToLog('Взят в обработку пользователем - '.$user->name.' '.$user->surname);
        $model->update();

        User::sendBotNotifications($model,'взят в обработку пользователем - '.$user->name.' '.$user->surname);

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Show all comments of ticket
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionComments($id = null)
    {
        $model = Ticket::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Not found', 404);
        }

        return $this->renderPartial('_comments',compact('model'));
    }

    /**
     * User messages
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMessages($id = null)
    {
        /* @var $user User */
        $user = User::findOne(['id' => Yii::$app->user->id]);

        $ticket = Ticket::findOne((int)$id);

        if(empty($ticket)){
            throw new NotFoundHttpException('Not found', 404);
        }

        $model = new UserMessage();
        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->author_id = Yii::$app->user->id;
            $model->ticket_id = $ticket->id;
            $model->created_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->updated_by_id = Yii::$app->user->id;

            if($model->validate()){
                if($model->save()){
                    $ticket->appendToLog($user->getFullName()." оставил сообщение");
                    User::sendBotNotifications($ticket,$user->getFullName()." оставил сообщение");
                }
            }
        }

        $messages = UserMessage::find()->where(['ticket_id' => $ticket->id])->all();

        return $this->renderPartial('_messages',compact('ticket','messages','model'));
    }

    /**
     * Leave comment for
     * @param null $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionLeaveComment($id = null)
    {
        $ticket = Ticket::findOne((int)$id);

        if(empty($ticket)){
            throw new NotFoundHttpException('Not found', 404);
        }

        $model = new TicketComment();

        //if loaded and validated
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->author_id = Yii::$app->user->id;
            $model->ticket_id = $ticket->id;
            $model->created_at = date('Y-m-d H:i:s',time());
            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
            $model->updated_by_id = Yii::$app->user->id;
            $ok = $model->save();

            if($ok){
                $ticket->appendToLog($model->author->name.' '.$model->author->surname.' добавил комментарий');
                $model->updated_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $ticket->update();

                User::sendBotNotifications($ticket,'прокомментирован пользователем - '.$model->author->name.' '.$model->author->surname);
            }

            return 'OK';
        }

        return $this->renderAjax('_add_comment',compact('ticket','model'));
    }

    /**
     * Change status
     * @param $id
     * @param $status
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionStatus($id, $status)
    {
        $model = Ticket::findOne((int)$id);

        $statuses = [
            Constants::STATUS_DONE => 'Отработан',
            Constants::STATUS_IN_PROGRESS => 'В работе',
            Constants::STATUS_NEW => 'Новый',
        ];

        /* @var $user User */
        $user = Yii::$app->user->identity;

        if(empty($model) || !array_key_exists($status,$statuses) || ($user->role_id != Constants::ROLE_ADMIN  && $model->performer_id != $user->id)){
            throw new NotFoundHttpException('Not found', 404);
        }

        $model->status_id = $status;
        $model->updated_at = date('Y-m-d H:i:s', time());
        $model->appendToLog($user->name.' '.$user->surname.' сменил статус на "'.$statuses[$status].'"');
        $model->update();

        User::sendBotNotifications($model,',статус изменен пользователем '.$user->name.' '.$user->surname.' на "'.$statuses[$status].'"');

        if(Yii::$app->request->isAjax){
            return $this->actionTicketAjaxRefresh($model->id);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Render ticket-item block (used for ajax updating)
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTicketAjaxRefresh($id)
    {
        $model = Ticket::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException('Not found', 404);
        }

        return $this->renderAjax('_ticket_item',compact('model'));
    }

    /**
     * Change ticket's performer
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionChangePerformer($id)
    {
        $model = Ticket::findOne((int)$id);

        /* @var $user User */
        $user = Yii::$app->user->identity;

        if(empty($model) || $user->role_id != Constants::ROLE_ADMIN){
            throw new NotFoundHttpException('Not found', 404);
        }

        $oldPerformerId = $model->performer_id;
        $oldStatus = $model->status_id;

        if($model->load(Yii::$app->request->post())){
            if($model->validate()){
                $model->update();
                $model->refresh();

                if($oldPerformerId != $model->performer->id){
                    if(!empty($model->performer)){
                        if($oldStatus == Constants::STATUS_DONE || $oldStatus == Constants::STATUS_NEW){
                            $model->status_id = Constants::STATUS_IN_PROGRESS;
                        }
                        $model->appendToLog($user->name.' '.$user->surname.' дилегировал тикет '.$model->performer->name.' '.$model->performer->surname);
                        User::sendBotNotifications($model,'делегирован пользователем '.$user->name.' '.$user->surname.' на '.$model->performer->name.' '.$model->performer->surname);
                    }
                    else{
                        $model->status_id = Constants::STATUS_NEW;
                        $model->appendToLog($user->name.' '.$user->surname.' обнулил исполнителя');
                        User::sendBotNotifications($model,', исполнитель убран пользователем '.$user->name.' '.$user->surname);
                    }
                    $model->update();
                }

                return 'OK';
            }
        }

        return $this->renderAjax('_change_performer',compact('model'));
    }

    /**
     * Create ticket
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionCreate()
    {

        $model = new Ticket();

        /* @var $user User */
        $user = Yii::$app->user->identity;

        if($user->role_id != Constants::ROLE_ADMIN){
            throw new NotFoundHttpException('Not found', 404);
        }

        if($model->load(Yii::$app->request->post())){
            $model->files = UploadedFile::getInstances($model,'files');
            if($model->validate()){
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;
                $model->status_id = Constants::STATUS_NEW;
                $model->appendToLog('Создан тикет');
                User::sendBotNotifications($model,'создан пользователем '.$user->name.' '.$user->surname);
                $saved = $model->save();

                if($saved){
                    $model->createFilesFromUploaded();

                    if(!empty($model->performer)){
                        $model->status_id = Constants::STATUS_IN_PROGRESS;
                        $model->appendToLog($user->name.' '.$user->surname.' создал тикет для '.$model->performer->name.' '.$model->performer->surname);
                        User::sendBotNotifications($model,'создан пользователем '.$user->name.' '.$user->surname.' и назначен на '.$model->performer->name.' '.$model->performer->surname);
                        $model->files = null;
                        $model->update();
                    }
                }

                return $this->redirect(Url::to(['/admin/tickets/index']));
            }
        }

        return $this->render('edit',compact('model'));
    }

    /**
     * Delete ticket
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = Ticket::findOne((int)$id);

        /* @var $user User */
        $user = Yii::$app->user->identity;

        if(empty($model) || $user->role_id != Constants::ROLE_ADMIN){
            throw new NotFoundHttpException('Not found', 404);
        }

        foreach($model->ticketImages as $image){
            $image->deleteFile();
        }
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Get content of simple modal
     * @param $title
     * @param $content
     * @return string
     */
    private function getFastModal($title,$content)
    {
        return '<div class="modal-content"><div class="modal-header"><h4 class="modal-title">'.$title.'</h4></div><div class="modal-body"><p>'.$content.'</p></div><div class="modal-footer"><button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button></div></div>';
    }
}