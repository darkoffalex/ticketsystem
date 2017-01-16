<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticket_comment".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property integer $author_id
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $updated_by_id
 *
 * @property User $author
 * @property Ticket $ticket
 */
class TicketComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'author_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['text'], 'string'],
            [['text'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Тикет',
            'author_id' => 'Автор',
            'text' => 'Текст',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'created_by_id' => 'Создан (кем)',
            'updated_by_id' => 'Обновлен (кем)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }
}
