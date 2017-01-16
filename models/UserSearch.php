<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{

    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'name', 'surname'], 'string', 'max' => 255],
            [['status_id', 'role_id'], 'integer'],
            [['created_at', 'online_at'], 'date', 'format' => 'yyyy-MM-dd - yyyy-MM-dd']
        ];
    }

    /**
     * Build search query and return as result data provider
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $q = User::find();

        $this->load($params);

        if($this->validate()){

            if(!empty($this->username)){
                $q->andWhere(['like','username', $this->username]);
            }

            if(!empty($this->name)){
                $q->andWhere(['like','name', $this->name]);
            }

            if(!empty($this->surname)){
                $q->andWhere(['like','surname', $this->surname]);
            }

            if(!empty($this->role_id)){
                $q->andWhere(['role_id' => $this->role_id]);
            }

            if(!empty($this->last_online_at)){
                $range = explode(' - ',$this->last_online_at);
                $date_from = $range[0];
                $date_to = $range[1];
                $q->andWhere('last_online_at >= :from AND last_online_at <= :to',['from' => $date_from, 'to' => $date_to]);
            }

            if(!empty($this->created_at)){
                $range = explode(' - ',$this->created_at);
                $date_from = $range[0];
                $date_to = $range[1];
                $q->andWhere('created_at >= :from AND created_at <= :to',['from' => $date_from, 'to' => $date_to]);
            }
        }

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
    }
}