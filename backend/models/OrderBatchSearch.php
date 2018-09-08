<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OrderBatch;

/**
 * OrderBatchSearch represents the model behind the search form about `backend\models\OrderBatch`.
 */
class OrderBatchSearch extends OrderBatch
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'start_time', 'end_time'], 'integer'],
            [['car_id', 'car_name', 'car_driver_name', 'car_driver_phone', 'batch_no', 'batch_wms'], 'safe'],
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
        $query = OrderBatch::find();

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
            'user_id' => $this->user_id,
            'status' => $this->status,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'car_id', $this->car_id])
            ->andFilterWhere(['like', 'car_name', $this->car_name])
            ->andFilterWhere(['like', 'car_driver_name', $this->car_driver_name])
            ->andFilterWhere(['like', 'car_driver_phone', $this->car_driver_phone])
            ->andFilterWhere(['like', 'batch_no', $this->batch_no])
            ->andFilterWhere(['like', 'batch_wms', $this->batch_wms]);

        return $dataProvider;
    }
}
