<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use app\models\Ticket;
use app\models\TicketComment;
use app\models\TicketSearch;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class TicketsController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new TicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
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

        //ajax validation
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

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
                $ticket->appendToLog('Пользвоатель - '.$model->author->name.' '.$model->author->surname.' добавил комментарий');
                $model->updated_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $ticket->update();
            }

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_add_comment',compact('ticket','model'));
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