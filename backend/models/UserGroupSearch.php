<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserGroup;

/**
 * UserGroupSearch represents the model behind the search form about `backend\models\UserGroup`.
 */
class UserGroupSearch extends UserGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'domain_id', 'priority','is_select'], 'integer'],
            [['name', 'desc'], 'safe'],
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
        $query = UserGroup::find();

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
            'domain_id' => $this->domain_id,
            'priority' => $this->priority,
            'is_select'=>$this->is_select,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'desc', $this->desc]);

            $id = Yii::$app->user->identity->id;
            $rank=Yii::$app->user->identity->rank;
            $companyid=Yii::$app->user->identity->company_categroy_id;
            $isadmin = in_array($id, Yii::$app->params['through']);
            if($rank != 30 && $rank != 5 && !$isadmin){
                $domainid =  Yii::$app->user->identity->domain_id;
            }
            if(!$isadmin){
                $department_id = UserDepartment::find()->select('id')->where(['company'=>$companyid])->asArray()->column();
                $query->andFilterWhere([ 'department_id'=>$department_id]);
            }
            if(isset($domainid)){
                $query->andFilterWhere([ 'domain_id'=>$domainid]);
            }
            
            
            
        return $dataProvider;
    }
}
