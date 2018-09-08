<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CompanyCategroy;

/**
 * CompanyCategroySearch represents the model behind the search form about `backend\models\CompanyCategroy`.
 */
class CompanyCategroySearch extends CompanyCategroy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'createtime', 'area_id', 'domain_id', 'fly', 'type', 'review', 'staff_num', 'proxy_level', 'goods_num', 'failure', 'goods_type', 'service_type', 'product_type'], 'integer'],
            [['name', 'phone', 'license_num', 'register_money', 'business', 'business_ress', 'acting', 'service_area', 'distribution_merchant', 'distribution_car', 'distribution_staff', 'salas_business', 'license_image', 'user_image_negative', 'user_image_positive'], 'safe'],
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
        $query = CompanyCategroy::find();

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
            'status' => $this->status,
            'createtime' => $this->createtime,
            'area_id' => $this->area_id,
            'domain_id' => $this->domain_id,
            'fly' => $this->fly,
            'type' => $this->type,
            'review' => $this->review,
            'staff_num' => $this->staff_num,
            'proxy_level' => $this->proxy_level,
            'goods_num' => $this->goods_num,
            'failure' => $this->failure,
            'goods_type' => $this->goods_type,
            'service_type' => $this->service_type,
            'product_type' => $this->product_type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'license_num', $this->license_num])
            ->andFilterWhere(['like', 'register_money', $this->register_money])
            ->andFilterWhere(['like', 'business', $this->business])
            ->andFilterWhere(['like', 'business_ress', $this->business_ress])
            ->andFilterWhere(['like', 'acting', $this->acting])
            ->andFilterWhere(['like', 'service_area', $this->service_area])
            ->andFilterWhere(['like', 'distribution_merchant', $this->distribution_merchant])
            ->andFilterWhere(['like', 'distribution_car', $this->distribution_car])
            ->andFilterWhere(['like', 'distribution_staff', $this->distribution_staff])
            ->andFilterWhere(['like', 'salas_business', $this->salas_business])
            ->andFilterWhere(['like', 'license_image', $this->license_image])
            ->andFilterWhere(['like', 'user_image_negative', $this->user_image_negative])
            ->andFilterWhere(['like', 'user_image_positive', $this->user_image_positive]);

            $query->andFilterWhere(['fly'=>0]);
            
        return $dataProvider;
    }
}
