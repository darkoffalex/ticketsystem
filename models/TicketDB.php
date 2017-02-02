<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property integer $performer_id
 * @property integer $status_id
 * @property integer $type_id
 * @property integer $category_id
 * @property string $author_name
 * @property string $text
 * @property string $link
 * @property string $log
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $updated_by_id
 * @property string $phone_or_email
 * @property integer $author_id
 *
 * @property User $author
 * @property User $performer
 * @property TicketComment[] $ticketComments
 * @property TicketImage[] $ticketImages
 * @property UserMessage[] $userMessages
 */
class TicketDB extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['performer_id', 'status_id', 'type_id', 'category_id', 'created_by_id', 'updated_by_id', 'author_id'], 'integer'],
            [['text', 'link', 'log'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['author_name', 'phone_or_email'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['performer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'performer_id' => 'Performer ID',
            'status_id' => 'Status ID',
            'type_id' => 'Type ID',
            'category_id' => 'Category ID',
            'author_name' => 'Author Name',
            'text' => 'Text',
            'link' => 'Link',
            'log' => 'Log',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by_id' => 'Created By ID',
            'updated_by_id' => 'Updated By ID',
            'phone_or_email' => 'Phone Or Email',
            'author_id' => 'Author ID',
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
    public function getPerformer()
    {
        return $this->hasOne(User::className(), ['id' => 'performer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketComments()
    {
        return $this->hasMany(TicketComment::className(), ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketImages()
    {
        return $this->hasMany(TicketImage::className(), ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessages()
    {
        return $this->hasMany(UserMessage::className(), ['ticket_id' => 'id']);
    }
}
