<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Shop;

/**
 * ShopSearch represents the model behind the search form about `backend\models\Shop`.
 */
class ShopSearch extends Shop
{

    public $start_time;
    public $end_time;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shop_type', 'shop_source', 'shop_status', 'shop_priority', 'user_id', 'company_category_id', 'shop_review', 'shop_domain', 'createtime'], 'integer'],
            [['shop_name', 'name', 'phone', 'shop_longitude', 'user_name', 'shop_addr', 'shop_title', 'shop_describe'], 'safe'],
            [['shop_latitude', 'shop_image'], 'number'],
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
        $query = Shop::find();

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
            'shop_type' => $this->shop_type,
            'shop_source' => $this->shop_source,
            'shop_status' => $this->shop_status,
            'shop_priority' => $this->shop_priority,
            'shop_latitude' => $this->shop_latitude,
            'shop_image' => $this->shop_image,
            'user_id' => $this->user_id,
            'company_category_id' => $this->company_category_id,
            'shop_review' => $this->shop_review,
            'shop_domain' => $this->shop_domain,
        ]);

        $query->andFilterWhere(['like', 'shop_name', $this->shop_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'shop_longitude', $this->shop_longitude])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'shop_addr', $this->shop_addr])
            ->andFilterWhere(['like', 'shop_title', $this->shop_title])
            ->andFilterWhere(['like', 'shop_describe', $this->shop_describe])
            ->orderBy('createtime desc');
        $query->andFilterWhere(['between', 'createtime', $this->start_time, $this->end_time]);
        return $dataProvider;
    }
}
