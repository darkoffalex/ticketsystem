<?php

namespace app\models;

use GuzzleHttp\Psr7\UploadedFile;
use himiklab\thumbnail\EasyThumbnailImage;
use Yii;
use yii\helpers\Url;

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
            'ticket_id' => 'Заявка',
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

    /**
     * Checks if file is exist
     * @return bool
     */
    public function hasFile()
    {
        if(empty($this->filename)){
            return false;
        }
        return file_exists(Yii::getAlias('@webroot/uploads/img/'.$this->filename));
    }

    /**
     * Deletes uploaded file if exist
     * @return bool
     */
    public function deleteFile()
    {
        if(!$this->hasFile()){
            return false;
        }

        return unlink(Yii::getAlias('@webroot/uploads/img/'.$this->filename));
    }

    /**
     * Returns an URL to thumbnail file
     * @param int $width
     * @param int $height
     * @return bool|string
     */
    public function getThumbnailUrl($width = 100, $height = 100)
    {
        if(!$this->hasFile()){
            return false;
        }

        return EasyThumbnailImage::thumbnailFileUrl(Yii::getAlias('@webroot/uploads/img/'.$this->filename),$width,$height);
    }

    /**
     * Returns a full URL to image file
     * @return string
     */
    public function getFullUrl()
    {
        return Url::to('@web/uploads/img/'.$this->filename,true);
    }
}
