<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OrderDelivery;

/**
 * OrderDeliverySearch represents the model behind the search form about `backend\models\OrderDelivery`.
 */
class OrderDeliverySearch extends OrderDelivery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'user_id', 'member_id', 'status', 'scan_time', 'depart_time', 'sign_for_time', 'batch_no', 'batch_status', 'pay_sign_status'], 'integer'],
            [['car_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OrderDelivery::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'member_id' => $this->member_id,
            'status' => $this->status,
            'scan_time' => $this->scan_time,
            'depart_time' => $this->depart_time,
            'sign_for_time' => $this->sign_for_time,
            'batch_no' => $this->batch_no,
            'batch_status' => $this->batch_status,
            'pay_sign_status' => $this->pay_sign_status,
        ]);

        $query->andFilterWhere(['like', 'car_id', $this->car_id]);

        return $dataProvider;
    }
}
