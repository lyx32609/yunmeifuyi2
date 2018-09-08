<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WithdRate;

/**
 * WithdRateSearch represents the model behind the search form about `backend\models\WithdRate`.
 */
class WithdRateSearch extends WithdRate
{
    /**
     * @inheritdoc
     */
    public $username;
    public $name;
    public $start_time;
    public $end_time;
    public $company_id;
    public $department;
    public function rules()
    {
        return [
            [['id', 'pound_money', 'pound_percent', 'is_open'], 'integer'],
            [['is_open_which'], 'safe'],
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
        $query = WithdRate::find();

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
            'pound_money' => $this->pound_money,
            'pound_percent' => $this->pound_percent,
            'is_open' => $this->is_open,
        ]);

        $query->andFilterWhere(['like', 'is_open_which', $this->is_open_which]);

        return $dataProvider;
    }
}
