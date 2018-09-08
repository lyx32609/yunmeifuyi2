<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PutImei;

/**
 * PutImeiModel represents the model behind the search form about `backend\models\PutImei`.
 */
class PutImeiSearch extends PutImei
{
    public $times;
    public $username;
    public $name;
    public $start_time;
    public $end_time;
    public $area;
    public $city;
    public $department;
    public $company;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'submit_time', 'department_id', 'pass_time', 'status', 'company_categroy_id', 'is_read'], 'integer'],
            [['new_imei_number', 'old_imei_number', 'old_brand', 'old_submit_time', 'new_brand'], 'safe'],
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
        $query = PutImei::findBySql("select * from (select * from off_put_imei WHERE `status`='2' order by id desc) as A group BY A.user_id ORDER BY A.id DESC ")
            ->select([
                'A.*',
                'user.name',
            ])
            ->leftJoin(['user' => User::tableName()], 'user.id = setting.user_id');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'submit_time' => $this->submit_time,
            'department_id' => $this->department_id,
            'pass_time' => $this->pass_time,
            'status' => $this->status,
            'company_categroy_id' => $this->company_categroy_id,
            'is_read' => $this->is_read,
        ]);

        $query->andFilterWhere(['like', 'new_imei_number', $this->new_imei_number])
            ->andFilterWhere(['like', 'old_imei_number', $this->old_imei_number])
            ->andFilterWhere(['like', 'old_brand', $this->old_brand])
            ->andFilterWhere(['like', 'old_submit_time', $this->old_submit_time])
            ->andFilterWhere(['like', 'new_brand', $this->new_brand]);
//file_put_contents('d:/op',print_r($query->createCommand()->getRawSql(),true));
        return $dataProvider;
    }
}
