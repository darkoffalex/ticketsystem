<?php

namespace app\models;

use app\helpers\Help;
use Yii;
use yii\base\NotSupportedException;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use app\helpers\Constants;

/**
 * Class User
 * @property Ticket[] $ticketsOpen
 * @package app\models
 */
class User extends UserDB implements IdentityInterface
{
    public $password;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => Constants::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status_id' => Constants::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status_id' => Constants::STATUS_ENABLED,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $baseLabels = parent::attributeLabels();
        $baseLabels['username'] = 'Логин';
        $baseLabels['name'] = 'Имя';
        $baseLabels['surname'] = 'Фамилия';
        $baseLabels['created_at'] = 'Зарегистрирован';
        $baseLabels['email'] = 'Email';
        $baseLabels['status_id'] = 'Состояние';
        $baseLabels['role_id'] = 'Роль';
        $baseLabels['password'] = 'Пароль';
        return $baseLabels;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['username', 'unique'];
        $rules[] = ['password', 'required', 'on' => 'create'];
        $rules[] = ['password', 'string', 'min' => 6];

        return $rules;
    }

    /**
     * Get only opened tickets (created by this user)
     * @return \yii\db\ActiveQuery
     */
    public function getTicketsOpen()
    {
        return parent::getTickets()->where(['status_id' => Constants::STATUS_NEW])->orderBy('created_at ASC');
    }

    /**
     * Returns just tickets which have opened questions to user
     * @return Ticket[]
     */
    public function getTicketsAwaiting()
    {
        /* @var $undone Ticket[] */
        $undone = Ticket::find()
            ->with('userMessages')
            ->where('status_id != :done AND author_id = :author',['done' => Constants::STATUS_DONE, 'author' => $this->id])
            ->orderBy('created_at ASC')
            ->all();

        foreach($undone as $index => $ticket){
            if(!$ticket->hasOpenedQuestion()){
                unset($undone[$index]);
            }
        }

        return array_values($undone);
    }

    /**
     * Returns URL path to user avatar
     * @return string
     */
    public function getAvatar()
    {
        return !empty($this->fb_avatar_url) ? $this->fb_avatar_url : Url::to('@web/img/no_user.png');
    }

    /**
     * Send notification to all users who wants it
     * @param Ticket $ticket
     * @param string $message
     */
    public static function sendBotNotifications($ticket,$message){

        /* @var $ticket Ticket */
        $performerId = $ticket->performer_id;

        $q = User::find()->where('bot_user_id IS NOT NULL AND bot_notify_settings IS NOT NULL');
        if(!empty($performerId)){
            $q->andWhere(['like','bot_notify_settings', (string)$performerId])
                ->orWhere('bot_notify_settings = :all AND bot_user_id IS NOT NULL',['all' => 'all']);
        }else{
            $q->andWhere(['bot_notify_settings' => 'all']);
        }

        $message='Тикет No'. $ticket->id.'.('.Url::to(['/admin/tickets','id' => $ticket->id],true).') '.$message;

        /* @var $recipients User[] */
        $recipients = $q->all();
        $urls = [];

        if(!empty($recipients)){
            foreach($recipients as $rcp){
                $urls[] = Url::to(['/site/bot-send','recipient' => $rcp->bot_user_id,'message' => $message],true);
            }
        }

        Help::multicurl($urls);
    }

    /**
     * Gets bot's notification config
     * @return string
     */
    public function getBotConfig()
    {
        if(empty($this->bot_user_id)){
            return 'Бот не подключен';
        }

        $settings = explode(':',$this->bot_notify_settings);

        if(in_array('all',$settings)){
            return 'Вы получаете уведомления о всех тикетах';
        }

        $result = [];
        foreach($settings as $id){
            /* @var $user User */
            $user = User::find()->where(['id' => (int)$id])->one();
            if(!empty($user)){
                $result[] = $user->name.' '.$user->surname;
            }
        }

        if(!empty($result)){
            return 'Вы получаете уведомления о тикетах пользователей : '.implode(', ',$result);
        }

        return 'Вы не получаете уведмлений (бот подклюечен)';
    }

    /**
     * Name and surname in one string
     * @return string
     */
    public function getFullName()
    {
        return $this->name.' '.$this->surname;
    }

    /**
     * Initialises dialog with bot
     * @param $message
     */
    public function botInitDialogIfNeed($message = null)
    {
        if(BotDialogSession::find()->where(['user_id' => $this->id, 'user_msg_type' => Constants::BOT_SESSION_INIT])->count() == 0){
            BotDialogSession::deleteAll(['user_id' => $this->id]);

            $initialisation = new BotDialogSession();
            $initialisation -> created_at = date('Y-m-d H:i:s',time());
            $initialisation -> updated_at = date('Y-m-d H:i:s',time());
            $initialisation -> life_time = 3600;
            $initialisation -> user_msg_text = $message;
            $initialisation -> user_msg_type = Constants::BOT_SESSION_INIT;
            $initialisation -> user_id = $this->id;
            $initialisation -> save();
        }
    }

    /**
     * Get init message (word from which the dialogue has begun)
     * @return null|string
     */
    public function botGetInitMessage()
    {
        /* @var BotDialogSession $bds */
        $bds = BotDialogSession::find()->where(['user_id' => $this->id, 'user_msg_type' => Constants::BOT_SESSION_INIT])->one();
        return !empty($bds) ? $bds->user_msg_text : null;
    }

    /**
     * Sets init message (word from which the dialogue has begun, for example if it was empty while init)
     * @param $message
     * @throws \Exception
     */
    public function botSetInitMessage($message)
    {
        /* @var BotDialogSession $bds */
        $bds = BotDialogSession::find()->where(['user_id' => $this->id, 'user_msg_type' => Constants::BOT_SESSION_INIT])->one();
        $bds -> user_msg_text = $message;
        $bds -> updated_at = date('Y-m-d H:i:s',time());
        $bds -> update();
    }

    /**
     * Set selection of ticket in dialog story
     * @param $id
     * @param int $type
     * @throws \Exception
     */
    public function botSetTicketSelection($id, $type = Constants::BOT_NEED_SELECT_TICKET)
    {
        /* @var BotDialogSession $bds */
        $bds = BotDialogSession::find()->where(['user_id' => $this->id, 'user_msg_type' => $type])->one();
        $bds -> operable_ticket_id = $id;
        $bds -> updated_at = date('Y-m-d H:i:s',time());
        $bds -> update();
    }

    /**
     * Returns previously selected ticket id from dialog
     * @param int $type
     * @return int|null
     */
    public function botGetTicketSelection($type = Constants::BOT_NEED_SELECT_TICKET)
    {
        /* @var BotDialogSession $bds */
        $bds = BotDialogSession::find()->where(['user_id' => $this->id, 'user_msg_type' => $type])->one();
        return !empty($bds) ? $bds->operable_ticket_id : null;
    }

    /**
     * Ends bot dialog
     */
    public function botEndDialog()
    {
        BotDialogSession::deleteAll(['user_id' => $this->id]);
    }

    /**
     * Creates new item in bot-dialog session
     * @param null $message
     * @param $type
     * @param null $ticketId
     */
    public function botContinueDialog($message = null, $type, $ticketId = null)
    {
        $asking = new BotDialogSession();
        $asking -> created_at = date('Y-m-d H:i:s',time());
        $asking -> updated_at = date('Y-m-d H:i:s',time());
        $asking -> life_time = 3600;
        $asking -> user_msg_text = $message;
        $asking -> user_msg_type = $type;
        $asking -> operable_ticket_id = $ticketId;
        $asking -> user_id = $this->id;
        $asking -> save();
    }

    /**
     * Check what question asked bot last time
     */
    public function botLastDialogState()
    {
        /* @var $bds BotDialogSession */
        $bds = BotDialogSession::find()->where(['user_id' => $this->id])->orderBy('id DESC')->one();
        return $bds->user_msg_type;
    }

    /**
     * Sends message as bot if user bind
     * @param $message
     */
    public function botSendMessage($message)
    {
        if(!empty($this->bot_user_id)){
            Help::azsend($this->bot_user_id,$message);
        }
    }
}
