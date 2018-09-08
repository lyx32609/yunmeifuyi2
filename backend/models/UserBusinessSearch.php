<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserBusiness;

/**
 * UserBusinessSearch represents the model behind the search form about `backend\models\UserBusiness`.
 */
class UserBusinessSearch extends UserBusiness
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
            [['id', 'staff_num', 'time', 'domain_id'], 'integer'],
            [['customer_name', 'customer_tel', 'customer_type', 'customer_source', 'customer_state', 'customer_priority', 'customer_photo_str', 'customer_business_title', 'customer_business_describe', 'customer_user'], 'safe'],
            [['customer_longitude', 'customer_latitude'], 'number'],
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
        $query = UserBusiness::find()->orderBy('time desc')
            ->from(['business' => self::tableName()])
            ->select([
                'business.*',
                'user.name',
                'user.company_categroy_id',
                'user.department_id',
                'user.username',
                'user.domain_id'
            ])
            ->leftJoin(['user' => User::tableName()], 'user.username = business.staff_num');

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
            'customer_longitude' => $this->customer_longitude,
            'customer_latitude' => $this->customer_latitude,
            'staff_num' => $this->staff_num,
            'time' => $this->time,
            'domain_id' => $this->domain_id,
        ]);

        $query->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_tel', $this->customer_tel])
            ->andFilterWhere(['like', 'customer_type', $this->customer_type])
            ->andFilterWhere(['like', 'customer_source', $this->customer_source])
            ->andFilterWhere(['like', 'customer_state', $this->customer_state])
            ->andFilterWhere(['like', 'customer_priority', $this->customer_priority])
            ->andFilterWhere(['like', 'customer_photo_str', $this->customer_photo_str])
            ->andFilterWhere(['like', 'customer_business_title', $this->customer_business_title])
            ->andFilterWhere(['like', 'customer_business_describe', $this->customer_business_describe])
            ->andFilterWhere(['like', 'customer_user', $this->customer_user]);

        return $dataProvider;
    }
}
