<?php

namespace app\models;

use app\helpers\Constants;
use app\helpers\Help;
use Yii;
use yii\base\Exception;
use yii\helpers\StringHelper;
use yii\web\UploadedFile;


class Ticket extends TicketDB
{

    const SCENARIO_APPEND = 'append';

    /**
     * @var UploadedFile[]
     */
    public $files = [];

    /**
     * @var null|string
     */
    public $appended_text = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $baseRules = parent::rules();
        $baseRules[] = [['files'], 'each', 'rule' => ['file', 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024*2]];
        $baseRules[] = [['link'],'url'];
        $baseRules[] = [['text'], 'required', 'message' => 'Поле обязательно для заполнения'];
        $baseRules[] = [['appended_text'], 'required', 'message' => 'Поле обязательно для заполнения', 'on' => self::SCENARIO_APPEND];
        return $baseRules;
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
            'author_id' => 'Автор',
            'appended_text' => 'Сообщение'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessages()
    {
        return parent::getUserMessages()->orderBy('created_at ASC');
    }

    /**
     * Check if has opened admin question
     * @return bool
     */
    public function hasOpenedQuestion()
    {
        if($this->status_id == Constants::STATUS_DONE){
            return false;
        }

        $messages = $this->userMessages;
        if(!empty($messages) && in_array($messages[count($messages)-1]->author->role_id,[Constants::ROLE_ADMIN,Constants::ROLE_REDACTOR])){
            return true;
        }

        return false;
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

    /**
     * Returns firsts words of ticket's text
     * @param int $length
     * @return string
     */
    public function getExcerpt($length = 10)
    {
        return StringHelper::truncateWords(strip_tags($this->text),$length).'...';
    }
}
