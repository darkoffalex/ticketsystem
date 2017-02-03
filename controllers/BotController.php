<?php

namespace app\controllers;


use app\helpers\Constants;
use app\helpers\Help;
use app\models\User;
use pimax\FbBotApp;
use pimax\Messages\Message;
use Yii;
use app\components\Controller;
use yii\helpers\ArrayHelper;

class BotController extends Controller
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
     * Web-hook for a chat-bot
     * @throws \Exception
     */
    public function actionHook()
    {
        Help::log("bot.log","handled");
        Help::log("bot.log",json_encode($_REQUEST));

        $verifyToken = "d74sd87fff145tg";
        $pageKey = "EAAK8ytZB7P7oBAP7VO7h1uN2Kq9pL4BnVJArgZB52ZCN9CTKwol3YQE61QXtzTZCW46gVoUEiRFfq9QhiFpCaFG31m29VPytxZB4l9ZA6NMW7RDlZABfCZCYD5MRawpeFTvraZCEXIgA2wJBg3cuJSqZAqRDr2KRrTDniiLljiJqURnwZDZD";

        $bot = new FbBotApp($pageKey);

        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verifyToken) {
            echo $_REQUEST['hub_challenge'];
        }else{

            $dataJson = file_get_contents("php://input");
            $data = json_decode($dataJson, true);
            Help::log("bot.log",$dataJson."\n");

            if(!empty($data['entry'][0]['messaging'])){
                foreach ($data['entry'][0]['messaging'] as $item)
                {
                    if(!empty($item['message']) && empty($item['message']['is_echo'])){

                        $senderId = $item['sender']['id'];
                        $command = $item['message']['text'];

                        /* @var $user User */
                        $user = User::find()->where(['bot_user_id' => $senderId])->one();

                        //default response
                        $response = "Это бот поддержки бла-бла введите код для привязки бла-бла";

                        //if user not bind and code entered
                        if(empty($user) && is_numeric($command)){
                            /* @var $user User */
                            $user = User::find()->where(['bot_key' => (int)$command])->one();

                            if(!empty($user)){
                                $user->bot_user_id = $senderId;
                                $user->update();

                                if($user->role_id == Constants::ROLE_NEW){
                                    $response = "Вы привязали аккаунт к пользовлателю {$user->getFullName()} \n";
                                    $response .= "У вас права обычного пользователя. Вы можете получать уведомления о заявках, отвечать на вопросы поддержки, дополнять заявки";
                                }else{
                                    $response = "Вы привязали аккаунт к пользовлателю {$user->getFullName()} \n";
                                    $response .= "У вас права редактора/админа. Вы можете настроить уведомления, получать уведомления о всех заявка системы либо только о заявках какого-то конкретного пользователя";
                                }
                            }
                        //if user bind
                        }elseif(!empty($user)){

                            /* REGULAR FRONT USER HANDLER */
                            if($user->role_id == Constants::ROLE_NEW){
                                //TODO: show regular user's options

                            /* ADMIN-REDACTOR HANDLER */
                            }else{
                                $params = explode(' ',$command);
                                $cmd = ArrayHelper::getValue($params,0,null);
                                $param = ArrayHelper::getValue($params,1,null);

                                switch($cmd){
                                    case 'get':
                                        if($param == 'all'){
                                            $user->bot_notify_settings = 'all';
                                            $user->update();
                                            $response = "Настройки успешно обновлены. Вы будете получать уведомления о всех тикатах";
                                        }elseif(!empty($param)){
                                            $ids = explode(',',$param);
                                            $idsChecked = [];
                                            $names = [];
                                            /* @var $users User[] */
                                            $users = User::find()->where(['id' => $ids])->all();
                                            if(!empty($users)){
                                                foreach($users as $u){
                                                    $names[] = $u->getFullName();
                                                    $idsChecked[] = $u->id;
                                                }
                                                $user->bot_notify_settings = implode(":",$idsChecked);
                                                $response = "Настройки успешно обновлены. Вы будете получать уведомения о тикетах назначенных пользователям : ".implode(', ',$names);
                                            }else{
                                                $response = "Возникла ошибка. Ни один из указанных ID не был найден в базе тикет-системы.";
                                            }
                                        }else{
                                            $response = "Возникла ошибка. Убедитесь что бот привязан к вашему аккаунту в тикет-стстеме";
                                        }
                                        break;

                                    case 'info':
                                        $response = $user->getBotConfig();
                                        break;

                                    case 'unbind':
                                        $user->bot_user_id = null;
                                        $user->update();
                                        $response = "Вы успешно отвязали бота от вашего аккаунта. Вы не будете получать уведомления";
                                        break;
                                }
                            }
                        }


                        /* @var $message Message */
                        $message = new Message($senderId, $response);
                        $bot->send($message);
                    }
                }
            }
        }
    }
}