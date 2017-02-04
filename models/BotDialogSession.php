<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bot_dialog_session".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $user_msg_text
 * @property integer $user_msg_type
 * @property integer $operable_ticket_id
 * @property integer $life_time
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class BotDialogSession extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bot_dialog_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_msg_type', 'operable_ticket_id', 'life_time'], 'integer'],
            [['user_msg_text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'user_msg_text' => 'User Msg Text',
            'user_msg_type' => 'User Msg Type',
            'operable_ticket_id' => 'Operable Ticket ID',
            'life_time' => 'Life Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
