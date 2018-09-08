<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Percentum;

/**
 * PercentumSearch represents the model behind the search form about `backend\models\Percentum`.
 */
class PercentumSearch extends Percentum
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time'], 'integer'],
            [['old_per', 'new_per'], 'safe'],
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
        $query = Percentum::find();

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
            'flag'=>0,
            'department_id'=>$this->department_id
        ]);

        $query->andFilterWhere(['like', 'old_per', $this->old_per])
            ->andFilterWhere(['like', 'new_per', $this->new_per])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'name', $this->name]);
        $query->orderBy('time desc');
        $query->andFilterWhere(['between', 'time', $this->start_time, $this->end_time]);
        return $dataProvider;
    }
}
