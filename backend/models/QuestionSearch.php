<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Question;

/**
 * QuestionSearch represents the model behind the search form about `backend\models\Question`.
 */
class QuestionSearch extends Question
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'problem_id', 'author_id', 'group_id', 'create_time', 'company_id'], 'integer'],
            [['question_content', 'author', 'group'], 'safe'],
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
        $query = Question::find();

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
            'question_id' => $this->question_id,
            'problem_id' => $this->problem_id,
            'author_id' => $this->author_id,
            'group_id' => $this->group_id,
            'create_time' => $this->create_time,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'question_content', $this->question_content])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'group', $this->group]);

        return $dataProvider;
    }
}
