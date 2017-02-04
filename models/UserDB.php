<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $fb_id
 * @property string $fb_avatar_url
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property integer $role_id
 * @property integer $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $online_at
 * @property integer $created_by_id
 * @property integer $updated_by_id
 * @property string $bot_key
 * @property string $bot_user_id
 * @property string $bot_notify_settings
 * @property string $fb_profile_url
 * @property integer $last_bot_message_type
 * @property integer $last_bot_operable_ticket
 *
 * @property BotDialogSession[] $botDialogSessions
 * @property Ticket[] $tickets
 * @property Ticket[] $tickets0
 * @property TicketComment[] $ticketComments
 * @property UserMessage[] $userMessages
 */
class UserDB extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fb_id', 'fb_avatar_url', 'auth_key', 'password_hash', 'password_reset_token', 'bot_user_id'], 'string'],
            [['role_id', 'status_id', 'created_by_id', 'updated_by_id', 'last_bot_message_type', 'last_bot_operable_ticket'], 'integer'],
            [['created_at', 'updated_at', 'online_at'], 'safe'],
            [['username', 'name', 'surname', 'email', 'bot_key', 'bot_notify_settings', 'fb_profile_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fb_id' => 'Fb ID',
            'fb_avatar_url' => 'Fb Avatar Url',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'Email',
            'role_id' => 'Role ID',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'online_at' => 'Online At',
            'created_by_id' => 'Created By ID',
            'updated_by_id' => 'Updated By ID',
            'bot_key' => 'Bot Key',
            'bot_user_id' => 'Bot User ID',
            'bot_notify_settings' => 'Bot Notify Settings',
            'fb_profile_url' => 'Fb Profile Url',
            'last_bot_message_type' => 'Last Bot Message Type',
            'last_bot_operable_ticket' => 'Last Bot Operable Ticket',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBotDialogSessions()
    {
        return $this->hasMany(BotDialogSession::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets0()
    {
        return $this->hasMany(Ticket::className(), ['performer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketComments()
    {
        return $this->hasMany(TicketComment::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessages()
    {
        return $this->hasMany(UserMessage::className(), ['author_id' => 'id']);
    }
}
