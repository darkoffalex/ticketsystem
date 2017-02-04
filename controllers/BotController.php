<?php

namespace app\controllers;


use app\helpers\Constants;
use app\helpers\Help;
use app\models\BotDialogSession;
use app\models\Ticket;
use app\models\User;
use app\models\UserMessage;
use pimax\FbBotApp;
use pimax\Messages\Message;
use Yii;
use app\components\Controller;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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

        $verifyToken = Yii::$app->params['messengerVerifyToken'];
        $pageKey = Yii::$app->params['messengerPageKey'];

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

                                //if user wants just unbind bot
                                if($command == 'unbind'){
                                    $user->bot_user_id = null;
                                    $user->update();
                                    $response = "Вы успешно отвязали бота от вашего аккаунта. Вы не будете получать уведомления";
                                //if wants to talk
                                }elseif(!empty($command) || $command == '0'){

                                    $opened = $user->ticketsOpen;
                                    $awaitingAnswers = $user->getTicketsAwaiting();

                                    //if user has no tickets opened
                                    if(empty($opened) && empty($awaitingAnswers)){
                                        $link = Url::to(['/site/complaint'],true);
                                        $response = "На данный момент у вас нет открытых заявок или вопросов от поддержки. Чтобы создать заявку пройдите по ссылке - {$link} \n\n";
                                        $response .= "Чтобы отвязать бота от вашего аккаунта и больше не получать уведомления напишите unbind";
                                        $user->botEndDialog();
                                    //if has some opened tickets
                                    }else{
                                        $user->botInitDialogIfNeed($command);

                                        try{
                                            if($user->botLastDialogState() == Constants::BOT_SESSION_INIT){
                                                if(count($opened) > 1){
                                                    $user->botContinueDialog(null,Constants::BOT_NEED_APPEND);
                                                    $response = "Вы хотите дополнить одну из открытых заявок выше указанным текстом ? \n\n";
                                                    $response.= "1 - дополнить \n";
                                                    $response.= "2 - отменить \n";
                                                }else{
                                                    $ticket = $opened[0];
                                                    $excerpt = $ticket->getExcerpt();
                                                    $user->botContinueDialog(null,Constants::BOT_NEED_APPEND,$ticket->id);
                                                    $response = "Вы хотите дополнить заявку №{$ticket->id} \n\n{$excerpt} \n\nвыше указанным текстом ? \n\n";
                                                    $response.= "1 - дополнить \n";
                                                    $response.= "2 - отменить \n";
                                                }
                                            }elseif($user->botLastDialogState() == Constants::BOT_NEED_APPEND){
                                                if($command != '2' && $command != '1'){
                                                    $response = "Опция '{$command}' отсутствует. Пожалуйста выберите из списка доступных опций \n\n";
                                                    $response.= "1 - дополнить \n";
                                                    $response.= "2 - отменить \n";
                                                }elseif($command == '2'){
                                                    $response = "Отменено. Ваша заявка не будет дополнена";
                                                    $user->botEndDialog();
                                                }else{
                                                    if(count($opened) > 1){
                                                        $response = "Выберите какую именно заявку вы хотели бы дополнить : \n\n";
                                                        foreach($opened as $ticket){
                                                            $excerpt = $ticket->getExcerpt();
                                                            $response .= "№ {$ticket->id} : \n $excerpt \n\n";
                                                        }
                                                        $response .= "0 - Отмена";
                                                        $user->botContinueDialog(null,Constants::BOT_NEED_SELECT_TICKET);
                                                    }else{
                                                        $ticket = $opened[0];
                                                        $excerpt = $ticket->getExcerpt();

                                                        $ticket->updated_at = date('Y-m-d H:i:s',time());
                                                        $ticket->updated_by_id = $user->id;
                                                        $ticket->text.= "<br><br><span style='font-style: italic'>Дополнено {$ticket->created_at}</span><br>".$user->botGetInitMessage();
                                                        $ticket->appendToLog("Пользователь - ".$ticket->author->getFullName()." дополнил сообщение");
                                                        $ticket->update();

                                                        //User::sendBotNotifications($ticket,"Пользователь - ".$ticket->author->getFullName()." дополнил сообщение");

                                                        $response = "Заявка №{$ticket->id} ({$excerpt}) успешно дополнена! \n";
                                                        $user->botEndDialog();
                                                    }
                                                }
                                            }elseif($user->botLastDialogState() == Constants::BOT_NEED_SELECT_TICKET){
                                                $ticketsArr = ArrayHelper::map($opened,'id','text');
                                                if($command == '0'){
                                                    $response = "Отменено. Ваша заявка не будет дополнена";
                                                    $user->botEndDialog();
                                                }elseif(!array_key_exists((int)$command,$ticketsArr)){
                                                    $response = "Заявка '{$command}' отсутствует. Пожалуйста выберите из списка доступных заявок \n\n";
                                                    foreach($opened as $ticket){
                                                        $excerpt = $ticket->getExcerpt();
                                                        $response .= "№ {$ticket->id} - $excerpt \n\n";
                                                    }
                                                    $response .= "0 - Отмена";
                                                }else{
                                                    /* @var $ticket Ticket */
                                                    $ticket = Ticket::find()->where(['id' => (int)$command])->one();
                                                    if(!empty($ticket)){
                                                        $excerpt = $ticket->getExcerpt();

                                                        $ticket->updated_at = date('Y-m-d H:i:s',time());
                                                        $ticket->updated_by_id = $user->id;
                                                        $ticket->text.= "<br><br><span style='font-style: italic'>Дополнено {$ticket->created_at}</span><br>".$user->botGetInitMessage();
                                                        $ticket->appendToLog("Пользователь - ".$ticket->author->getFullName()." дополнил сообщение");
                                                        $ticket->update();

                                                        //User::sendBotNotifications($ticket,"Пользователь - ".$ticket->author->getFullName()." дополнил сообщение");

                                                        $response = "Заявка №{$ticket->id} ({$excerpt}) успешно дополнена! \n";
                                                        $user->botEndDialog();
                                                    }
                                                }
                                            }elseif($user->botLastDialogState() == Constants::BOT_NEED_ANSWER){
                                                $user->botSetInitMessage($command);

                                                if(!empty($awaitingAnswers)){
                                                    $ticket = $awaitingAnswers[0];
                                                    $lastQuestion = $ticket->userMessages[count($ticket->userMessages)-1]->message;
                                                    $excerpt = $ticket->getExcerpt();

                                                    $response = "Вы хотите ответить по заявке №{$ticket->id} \n\n{$excerpt} \n\nна вопрос от поддержки \n\n{$lastQuestion} \n\nвыше указанным текстом ? \n\n";
                                                    $response.= "1 - да \n";
                                                    $response.= "2 - нет \n";
                                                    $user->botContinueDialog(null,Constants::BOT_NEED_CONFIRM_ANSWER);
                                                }else{
                                                    $response = "Упс! Похоже пока вы думали все заявки ожидающие ответа были закрыты были закрыты!";
                                                    $user->botEndDialog();
                                                }
                                            }elseif($user->botLastDialogState() == Constants::BOT_NEED_CONFIRM_ANSWER){

                                                if($command != '2' && $command != '1'){
                                                    $response = "Опция '{$command}' отсутствует. Пожалуйста выберите из списка доступных опций \n\n";
                                                    $response.= "1 - да (ответить) \n";
                                                    $response.= "2 - нет (отмена) \n";
                                                }elseif($command == '2'){
                                                    $response = "Отменено. Ваш ответ не будет добавлен. Можете написать новый ответ";
                                                    $user->botEndDialog();
                                                    $user->botInitDialogIfNeed();
                                                    $user->botContinueDialog(null,Constants::BOT_NEED_ANSWER);
                                                }else{

                                                    if(!empty($awaitingAnswers[0])){
                                                        $ticket = $awaitingAnswers[0];
                                                        $excerpt = $ticket->getExcerpt();
                                                        $msg = $user->botGetInitMessage();

                                                        $msgItem = new UserMessage();
                                                        $msgItem -> message = $msg;
                                                        $msgItem -> ticket_id = $ticket->id;
                                                        $msgItem -> author_id = $ticket->author_id;
                                                        $msgItem -> created_at = date('Y-m-d H:i:s',time());
                                                        $msgItem -> updated_at = date('Y-m-d H:i:s',time());
                                                        $msgItem -> created_by_id = $ticket->author_id;
                                                        $msgItem -> updated_by_id = $ticket->author_id;
                                                        $msgItem -> save();

                                                        $ticket->appendToLog("Автор заявки добавил новое сообщение");

                                                        if(count($awaitingAnswers) > 1){
                                                            $response = "Ответ был успешно добавлен! Вы можете ответить на оставшиеся вопросы поддержки.";
                                                            $user->botEndDialog();
                                                            $user->botInitDialogIfNeed();
                                                            $user->botContinueDialog(null,Constants::BOT_NEED_ANSWER);
                                                        }else{
                                                            $response = "Ответ был успешно добавлен!";
                                                            $user->botEndDialog();
                                                        }
                                                    }else{
                                                        $response = "Упс! Похоже пока вы думали все заявки ожидающие ответа были закрыты были закрыты!";
                                                        $user->botEndDialog();
                                                    }
                                                }
                                            }else{
                                                $response = "Произошла какая-то дичь! Сообщите об этом разработчику, пусть проверит БД и выяснит как такое вообще могло случиться";
                                            }
                                        }catch (Exception $ex){
                                            $response = $ex->getMessage();
                                        }
                                    }
                                }else{
                                    $response = "Ты втираешь мне какую-то дичь!";
                                }


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

                                    default:
                                        $response = "Судя по всему вы ввели не верную команду. Список доступных команд для администраторов/редакторов : \n\n";
                                        $response .= "get [all|1|2,3,4] - настроить уведомления. all - получать о всех тикетах, или же тикетах конерктных пользователей. \n\n";
                                        $response .= "info - просмотреть текущие настройки оповещений \n\n";
                                        $response .= "unbind - отвязать бота от аккаунта. Не получать более уведомлений о тикетах.";
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