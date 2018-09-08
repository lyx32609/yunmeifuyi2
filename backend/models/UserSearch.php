<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\User;
use backend\models\AuthAssignment;

class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public $area;
    public $company_categroy_id;
    public $company_id;
    public $item_name;

    public function rules()
    {
        return [
            [['id', 'staff_code', 'token_createtime', 'domain_id', 'group_id', 'department_id', 'is_select', 'rank'], 'integer'],
            [['username', 'password', 'access_token', 'name', 'phone', 'head'], 'safe'],
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
        $query = User::find()
            ->from(['setting' => self::tableName()])
            ->select([
            'setting.*',
            'auth_assignment.item_name',
        ])
        ->leftJoin(['auth_assignment' => AuthAssignment::tableName()], 'auth_assignment.user_id = setting.id');
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'staff_code' => $this->staff_code,
            'token_createtime' => $this->token_createtime,
            'domain_id' => $this->domain_id,
            'group_id' => $this->group_id,
            'department_id' => $this->department_id,
            'is_select' => $this->is_select,
            'rank' => $this->rank,
            'item_name' => $this->item_name,
        ]);
        
        

        $id = Yii::$app->user->identity->id;
        $rank=Yii::$app->user->identity->rank;
        $isadmin = in_array($id, Yii::$app->params['through']);
        if($rank != 30 && $rank != 5 && !$isadmin){
            $domainid =  Yii::$app->user->identity->domain_id;
        }
        if(isset($domainid)){
            $query->andFilterWhere([ 'domain_id'=>$domainid]);
        }
        if(!$isadmin){
            $companyid=Yii::$app->user->identity->company_categroy_id;
            $query->andFilterWhere([ 'company_categroy_id'=>$companyid]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'head', $this->head]);  
        return $dataProvider;
    }
}
