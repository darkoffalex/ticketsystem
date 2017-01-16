<?php

namespace app\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\ContactForm;
use app\models\Ticket;
use Yii;
use app\components\Controller;
use yii\web\UploadedFile;

class SiteController extends Controller
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
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Feedback - complaint (create ticket)
     * @return string
     */
    public function actionComplaint()
    {
        $model = new Ticket();

        if($model->load(Yii::$app->request->post())){
            $model->files = UploadedFile::getInstances($model,'files');
            if($model->validate()){
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->status_id = Constants::STATUS_NEW;
                $model->appendToLog('Создан тикет');
                $saved = $model->save();

                if($saved){
                    $model->createFilesFromUploaded();
                }

                Yii::$app->session->setFlash('contactFormSubmitted',true);
            }
        }

        return $this->render('feedback_ticket',compact('model'));
    }

    /**
     * Where to get modal window (depending on browser)
     * @return string
     */
    public function actionWhereToGet()
    {
        $device = Yii::getAlias('@device');

        if($device == 'mobile'){
            return $this->renderPartial('_where_to_get_mobile');
        }

        return $this->renderPartial('_where_to_get_desktop');
    }

    /**
     * Feedback - offer (send email)
     * @return string
     */
    public function actionOffer()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIL_TYPE_OFFER;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    /**
     * Feedback - comment (send email)
     * @return string
     */
    public function actionComment()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIL_TYPE_COMMENT;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    /**
     * Feedback - question (send email)
     * @return string
     */
    public function actionQuestion()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIl_TYPE_QUESTION;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    /**
     * Processing email - form submit action
     * @param $model
     */
    public function commonEmailProcessing($model)
    {
        /* @var $model ContactForm */

        if($model->load(Yii::$app->request->post())){
            $model->files = UploadedFile::getInstances($model,'files');
            if($model->validate()){
                $model->contact();
                Yii::$app->session->setFlash('contactFormSubmitted',true);
            }
        }
    }

}
