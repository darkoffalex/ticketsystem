<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

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
 * @property string $phone_or_email
 * @property string $link
 * @property string $log
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $updated_by_id
 *
 * @property User $performer
 * @property TicketComment[] $ticketComments
 * @property TicketImage[] $ticketImages
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile[]
     */
    public $files = [];

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
            [['performer_id', 'status_id', 'type_id', 'category_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['text', 'link', 'log'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['author_name','phone_or_email'], 'string', 'max' => 255],
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
            'performer_id' => 'Исполнитель',
            'status_id' => 'Состояние',
            'type_id' => 'Тип',
            'category_id' => 'Категория',
            'author_name' => 'Имя',
            'phone_or_email' => 'Телефон и/или email',
            'text' => 'Сообщение',
            'link' => 'Ссылка пользователя',
            'log' => 'Лог',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'created_by_id' => 'Создан (кем)',
            'updated_by_id' => 'Обновлен (кем)',
        ];
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
}
