<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\WithdSetting;

/**
 * WithdSettingSeach represents the model behind the search form about `backend\models\WithdSetting`.
 */
class WithdSettingSeach extends WithdSetting
{
    public $start_time;
    public $end_time;
    public $username;
    public $company_id;
    public $department;
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'set_uid', 'set_before', 'set_after', 'set_time'], 'integer'],
            [['set_cont'], 'safe'],
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
        $query = WithdSetting::find()
            ->from(['w' => self::tableName()])
            ->select([
            'w.*',   
            'user.name',
            'user.username',
            'd.name as department'
        ])
        ->leftJoin(['user' => User::tableName()], 'user.id = w.set_uid')
        ->leftJoin(['d' => UserDepartment::tableName()], 'd.id = w.set_department_id');

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
            'set_uid' => $this->set_uid,
            'set_before' => $this->set_before,
            'set_after' => $this->set_after,
            'set_time' => $this->set_time,
            'name' => $this->name,
            'username' => $this->name,
            'department' => $this->department
        ]);

        $query->andFilterWhere(['like', 'set_cont', $this->set_cont]);

        return $dataProvider;
    }
}
