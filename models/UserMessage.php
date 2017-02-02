<?php

namespace app\models;

use Yii;

class UserMessage extends UserMessageDB
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $baseRules = parent::rules();
        $baseRules[] = [['message'], 'required', 'message' => 'Поле обязательно для заполнения'];
        return $baseRules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Сообщение',
            'author_id' => 'Автор',
            'ticket_id' => 'Тикет',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'created_by_id' => 'Создан (кем)',
            'updated_by_id' => 'Обновлен (кем)',
        ];
    }
}
