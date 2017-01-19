<?php

namespace app\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\ContactForm;
use app\models\Ticket;
use app\models\User;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphPicture;
use pimax\FbBotApp;
use Yii;
use app\components\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;
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
     * Index page
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Sending message from bot to recipient (used for async cURL)
     * @param $recipient
     * @param $message
     */
    public function actionBotSend($recipient,$message)
    {
        Help::azsend($recipient,$message);
    }

    /**
     * Bot hook
     * @param $command
     * @param null $param
     * @param null $sender
     * @return string
     * @throws \Exception
     */
    public function actionBotHook($command, $param = null, $sender = null)
    {
        if($command == 'bind'){
            /* @var $user User */
            $user = User::find()->where('bot_key = :key && bot_key IS NOT NULL',['key' => $param])->one();
            if(!empty($user) && !empty($sender)){
                $user->bot_user_id = $sender;
                $user->bot_notify_settings = 'all';
                $user->update();
                return 'Бот был приявязан к пользователю тикет-системы под именем '.$user->name.' '.$user->surname;
            }else{
                return 'Возникла ошибка. Введен неверный ключ';
            }
        }elseif($command == 'get'){
            /* @var $user User */
            $user = User::find()->where('bot_user_id = :usr && bot_user_id IS NOT NULL',['usr' => $sender])->one();
            if(!empty($user) && !empty($param)){
                if($param == 'all'){
                    $user->bot_notify_settings = 'all';
                    $user->update();
                    return 'Настройки успешно обновлены. Вы будете получать уведомления о всех тикатах';
                }else{
                    $ids = explode(',',$param);
                    $names = [];
                    $newIds = [];
                    foreach($ids as $id){
                        /* @var $u User */
                        $u = User::find()->where(['id' => (int)$id])->one();
                        if(!empty($u)){
                            $names[] = $u->name.' '.$u->surname;
                            $newIds[] = $u->id;
                        }
                    }
                    if(!empty($ids)){
                        $user->bot_notify_settings = implode(":",$newIds);
                        $user->update();
                        return 'Настройки успешно обновлены. Вы будете получать уведомения о тикетах назначенных пользователям : '.implode(', ',$names);
                    }else{
                        return 'Возникла ошибка. Ни один из указанных ID не был найден в базе тикет-системы.';
                    }
                }
            }else{
                return 'Возникла ошибка. Убедитесь что бот привязан к вашему аккаунту в тикет-стстеме';
            }
        }elseif($command == 'info'){
            /* @var $user User */
            $user = User::find()->where('bot_user_id = :usr && bot_user_id IS NOT NULL',['usr' => $sender])->one();
            if(!empty($user)){
                return $user->getBotConfig();
            }else{
                return 'Возникла ошибка. Убедитесь что бот привязан к вашему аккаунту в тикет-стстеме';
            }
        }elseif($command == 'unbind'){
            /* @var $user User */
            $user = User::find()->where('bot_user_id = :usr && bot_user_id IS NOT NULL',['usr' => $sender])->one();
            if(!empty($user)){
                $user->bot_user_id = '';
                $user->update();
                return 'Вы успешно отвязали бота от вашего аккаунта. Вы не будете получать уведомления';
            }else{
                return 'Возникла ошибка. Убедитесь что бот привязан к вашему аккаунту в тикет-стстеме';
            }
        }

        return 'Возникла ошибка. Проверьте правильность команды';
    }

    /**
     * Login with Facebook
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     */
    public function actionFbLogin()
    {
        /* @var $social \kartik\social\Module */
        $social = Yii::$app->getModule('social');
        $fb = $social->getFb();

        try {
            $helper = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken();
        } catch(FacebookSDKException $e) {
            throw new NotAcceptableHttpException($e->getMessage(),'402');
        }

        if (isset($accessToken)) {
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,picture', $accessToken);
            $data = $response->getGraphUser()->asArray();

            /* @var $picture GraphPicture */
            $picture = $response->getGraphUser()->getPicture();

            if(!empty($data) && !empty($data['id'])){

                //try find
                /* @var $user User */
                $user = User::find()->where(['fb_id' => ArrayHelper::getValue($data,'id')])->one();

                if(empty($user)){
                    //create user
                    $user = new User();
                    $user->fb_id = ArrayHelper::getValue($data,'id');
                    $user->email = ArrayHelper::getValue($data,'email');
                    $user->name = ArrayHelper::getValue($data,'first_name');
                    $user->surname = ArrayHelper::getValue($data,'last_name');
                    $user->created_at = date('Y-m-d H:i:s',time());
                    $user->updated_at = date('Y-m-d H:i:s',time());
                    $user->username = ArrayHelper::getValue($data,'email',$user->fb_id);
                    $user->password_hash = Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString(6));
                    $user->auth_key = Yii::$app->security->generateRandomString();
                    $user->fb_avatar_url = $picture->getUrl();
                    $user->status_id = Constants::STATUS_ENABLED;
                    $user->role_id = Constants::ROLE_NEW;
                    $ok = $user->save();
                }else{
                    $ok = true;
                    $user->fb_avatar_url = $picture->getUrl();
                    $user->updated_at = date('Y-m-d H:i:s',time());
                    $user->updated_by_id = $user->id;
                    $user->online_at = date('Y-m-d H:i:s',time());
                    $user->update();
                }

                //if saved or found - login
                if($ok){
                    Yii::$app->user->login($user);
                }

                //go to admin panel
                return $this->redirect(Url::to(['/admin/main/index']));
            }

            //log error if needed
        }elseif ($helper->getError()) {
            Help::log('auth.log',$helper->getError());
            Help::log('auth.log',$helper->getErrorCode());
            Help::log('auth.log',$helper->getErrorReason());
            Help::log('auth.log',$helper->getErrorDescription());
        }

        //back to main page
        return $this->redirect(Url::to(['/site/index']));
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
                    User::sendBotNotifications($model,'создан');
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
