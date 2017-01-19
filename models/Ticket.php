<?php

namespace app\models;

use app\helpers\Help;
use Yii;
use yii\base\Exception;
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

            [['link'],'url'],
            [['phone_or_email', 'text', 'author_name'], 'required', 'message' => 'Поле обязательно для заполнения'],
            [['files'], 'each', 'rule' => ['file', 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024*2]]
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
            'link' => 'Ссылка',
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
        return $this->hasMany(TicketComment::className(), ['ticket_id' => 'id'])->orderBy('created_at ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketImages()
    {
        return $this->hasMany(TicketImage::className(), ['ticket_id' => 'id']);
    }

    /**
     * Appends message to log with current date
     * @param $message
     */
    public function appendToLog($message)
    {
        $log = json_decode($this->log,true);
        $log[] = date('Y.m.d, H:i', time()).' '.$message;
        $this->log = json_encode($log);
    }

    /**
     * Returns log as string
     * @param string $separator
     * @param null $length
     * @return string
     */
    public function getLogAsString($separator = "\n",$length = null)
    {
        $logStr = "";
        $log = json_decode($this->log,true);
        $log = array_reverse($log);

        if(!empty($length)){
            $log = array_slice($log,0,$length,true);
        }

        foreach($log as $action){
            $logStr .= $action.$separator;
        }

        return $logStr;
    }

    /**
     * Uploads attached files and creates file-items in database
     */
    public function createFilesFromUploaded()
    {
        if(!empty($this->files)){
            foreach($this->files as $file){
                if($file->size > 0){
                    try{
                        $name = Yii::$app->security->generateRandomString(16).'.'.$file->extension;
                        if($file->saveAs(Yii::getAlias('@webroot/uploads/img/'.$name))){
                            $img = new TicketImage();
                            $img->created_at = date('Y-m-d H:i:s',time());
                            $img->updated_at = date('Y-m-d H:i:s',time());
                            $img->created_by_id = $this->created_by_id;
                            $img->updated_by_id = $this->updated_by_id;
                            $img->ticket_id = $this->id;
                            $img->file = null;
                            $img->filename = $name;
                            $img->original_filename = $file->name;
                            $img->name = $file->name;
                            $img->mime_type = $file->type;
                            $img->save();
                        }
                    }catch (Exception $ex){
                        Help::log('uploads.log',$ex->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Returns array for using in lightbox widget
     * @return array
     */
    public function getImageFilesData()
    {
        $result = [];

        if(!empty($this->ticketImages)){
            foreach($this->ticketImages as $image){
                if($image->hasFile()){
                    $result[] = [
                        'thumb' => $image->getThumbnailUrl(),
                        'original' => $image->getFullUrl(),
                        'title' => $image->name,
                        'thumbOptions' => ['class' => 'img-thumbnail']
                    ];
                }
            }
        }

        return $result;
    }
}
