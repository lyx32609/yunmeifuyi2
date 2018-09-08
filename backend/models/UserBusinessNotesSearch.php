<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserBusinessNotes;

/**
 * UserBusinessNotesSearch represents the model behind the search form about `backend\models\UserBusinessNotes`.
 */
class UserBusinessNotesSearch extends UserBusinessNotes
{
    public $area;
    public $city;
    public $name;
    public $company_id;
    public $department;
    public $username;
    public $start_time;
    public $end_time;
    public $company_categroy_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'business_id', 'time'], 'integer'],
            [['staff_num', 'followup_text'], 'safe'],
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
//        $query = UserBusinessNotes::find()->orderBy('time desc');
        $query = UserBusinessNotes::find()->orderBy('time desc')
            ->from(['businessnotes' => self::tableName()])
            ->select([
                'businessnotes.*',
                'user.name',
                'user.company_categroy_id',
                'user.department_id',
                'user.username',
                'user.domain_id'
            ])
            ->leftJoin(['user' => User::tableName()], 'user.username = businessnotes.staff_num');

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
            'business_id' => $this->business_id,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'staff_num', $this->staff_num])
            ->andFilterWhere(['like', 'followup_text', $this->followup_text]);

        return $dataProvider;
    }
}
