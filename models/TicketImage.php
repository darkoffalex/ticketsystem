<?php

namespace app\models;

use GuzzleHttp\Psr7\UploadedFile;
use Yii;

/**
 * This is the model class for table "ticket_image".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property string $name
 * @property string $filename
 * @property string $original_filename
 * @property string $mime_type
 * @property integer $priority
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $updated_by_id
 *
 * @property Ticket $ticket
 */
class TicketImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_image';
    }

    /**
     * @var UploadedFile
     */
    public $file = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'priority', 'created_by_id', 'updated_by_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'filename', 'original_filename', 'mime_type'], 'string', 'max' => 255],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],

            [['file'], 'file', 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024]
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
            'name' => 'Название',
            'filename' => 'Имя файла',
            'original_filename' => 'Орининаьное имя файла',
            'mime_type' => 'Mime тип',
            'priority' => 'Приоритет',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'created_by_id' => 'Создан (кем)',
            'updated_by_id' => 'Обновлен (кем)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }
}
