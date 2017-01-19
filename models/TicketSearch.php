<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\data\ActiveDataProvider;

class TicketSearch extends Ticket
{

    /**
     * Validation rules for search
     * @return array
     */
    public function rules()
    {
        return [
            [['author_name', 'text', 'phone_or_email'], 'string', 'max' => 255],
            [['performer_id', 'status_id'], 'integer'],
            [['created_at'], 'date', 'format' => 'yyyy-MM-dd - yyyy-MM-dd']
        ];
    }

    /**
     * Build search query and return as result data provider
     * @param $params
     * @param null $id
     * @return ActiveDataProvider
     */
    public function search($params,$id = null)
    {
        $q = self::find();

        if(!empty($id)){
            $q->where(['id' => (int)$id]);
        }else{
            $this->load($params);
            if($this->validate()){

                if(!empty($this->phone_or_email)){
                    $q->andWhere(['like','phone_or_email', $this->phone_or_email]);
                }

                if(!empty($this->author_name)){
                    $q->andWhere(['like','author_name', $this->author_name]);
                }

                if(!empty($this->text)){
                    $q->andWhere(['like','text', $this->text]);
                }

                if(!empty($this->status_id)){
                    $q->andWhere(['status_id' => $this->status_id]);
                }

                if(!empty($this->created_at)){
                    $range = explode(' - ',$this->created_at);
                    $date_from = $range[0];
                    $date_to = $range[1];
                    $q->andWhere('created_at >= :from AND created_at <= :to',['from' => $date_from, 'to' => $date_to]);
                }
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