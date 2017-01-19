<?php

namespace app\models;

use app\helpers\Constants;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $phone_or_email;
    public $message;

    public $type;

    /**
     * @var UploadedFile[]
     */
    public $files = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'phone_or_email', 'message'], 'string'],
            [['phone_or_email', 'message'], 'required', 'message' => 'Поле обязательно для заполнения'],
            [['files'], 'each', 'rule' => ['file', 'extensions' => ['png', 'jpg', 'gif', 'doc', 'xls', 'xlsx', 'docx', 'zip', 'rar', 'txt'], 'maxSize' => 1024*1024]]
        ];
    }

    /**
     * Returns a subject
     * @return mixed
     */
    public function getSubject()
    {
        $subjects = [
            Constants::EMAIL_TYPE_OFFER => 'Предложение',
            Constants::EMAIL_TYPE_COMMENT => 'Отзыв',
            Constants::EMAIl_TYPE_QUESTION => 'Вопрос',
        ];

        return ArrayHelper::getValue($subjects,$this->type,'Предложение');
    }

    /**
     * Return a body
     * @return string
     */
    public function getBody()
    {
        $text = "Контактные данные: \n";
        $text.= "Телефон/email - {$this->phone_or_email} \n";
        $text.= "Имя - {$this->name} \n";
        $text.= "\n\n";
        $text.= "Сообщение:\n";
        $text.= strip_tags($this->message);

        return $text;
    }


    /**
     * Returns email of first admin
     * @return string
     */
    public function getFirstAdminEmail()
    {
        /* @var $admin User */
        $admin = User::find()->where(['status_id' => Constants::STATUS_ENABLED, 'role_id' => Constants::ROLE_ADMIN])->orderBy('id ASC')->one();
        return $admin->email;
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        $labels = [
            'name' => 'Имя',
            'phone_or_email' => 'Телефон и/или email',
            'files' => 'Файл'
        ];

        switch($this->type){
            case Constants::EMAIL_TYPE_OFFER:
            default:
                $labels['message'] = 'Что бы в хотели предложить ?';
                break;
            case Constants::EMAIL_TYPE_COMMENT:
                $labels['message'] = 'Мы очень рады вашим отзывам! Они помогают нам стать лучше. А ваши благодарности говорят о том, что мы работаем не зря!';
                break;
            case Constants::EMAIl_TYPE_QUESTION:
                $labels['message'] = 'Напишите ваш вопрос команде «Единой сети РА» и мы ответим вам в ближайшее время!';
                break;
        }

        return $labels;
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @return bool whether the model passes validation
     */
    public function contact()
    {
        $composed = Yii::$app->mailer->compose()
            ->setTo($this->getFirstAdminEmail())
            ->setFrom([Yii::$app->params['adminEmail'] => $this->name])
            ->setSubject($this->getSubject())
            ->setTextBody($this->getBody());

        if(!empty($this->files)){
            foreach($this->files as $file){
                $composed->attach($file->tempName,['fileName' => $file->name, 'contentType' => $file->type]);
            }
        }

        return $composed->send();
    }
}
