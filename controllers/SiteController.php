<?php

namespace app\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\ContactForm;
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

    public function actionComplaint()
    {
        return $this->render('feedback_ticket');
    }

    /**
     * Feedback - offer
     * @return string
     */
    public function actionOffer()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIL_TYPE_OFFER;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    public function actionComment()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIL_TYPE_COMMENT;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    public function actionQuestion()
    {
        $model = new ContactForm();
        $model->type = Constants::EMAIl_TYPE_QUESTION;
        $this->commonEmailProcessing($model);
        return $this->render('feedback_email',compact('model'));
    }

    /**
     * Processing email form submit action
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
