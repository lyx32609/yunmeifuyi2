<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Problem;

/**
 * problemSearch represents the model behind the search form about `backend\models\problem`.
 */
class ProblemSearch extends problem
{
    /**
     * @inheritdoc
     */
    public $area;
    public $city;   
    public $user_name;
    public $start_time;
    public $end_time;
    public $department_id;
    public function rules()
    {
        return [
            [['problem_id', 'priority', 'create_time', 'user_id', 'problem_lock', 'update_time', 'company_id'], 'integer'],
            [['problem_title', 'problem_content', 'collaboration_department', 'user_name', 'area', 'city', 'department'], 'safe'],
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
        $query = Problem::find()
        ->from(['setting' => self::tableName()])
        ->select([
            'setting.*',
            'user.domain_id',
            'user.department_id',
            'user.name',
        ])
        ->leftJoin(['user' => User::tableName()], 'user.id = setting.user_id');

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
            'problem_id' => $this->problem_id,
            'priority' => $this->priority,
            'create_time' => $this->create_time,
            'user_id' => $this->user_id,
            'problem_lock' => $this->problem_lock,
            'update_time' => $this->update_time,
            'company_id' => $this->company_id,
            'department_id' => $this->department_id,
        ]);

        $query->andFilterWhere(['like', 'problem_title', $this->problem_title])
            ->andFilterWhere(['like', 'problem_content', $this->problem_content])
            ->andFilterWhere(['like', 'collaboration_department', $this->collaboration_department])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);
            //->andFilterWhere(['like', 'department', $this->department]);
        $rank = Yii::$app->user->identity->rank;
        $companyid = Yii::$app->user->identity->company_categroy_id;
        if(!in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//非超级管理员
        {
            if($rank = 30)//主公司经理
            {
                $child = CompanyCategroy::find()
                        ->select(["id"])
                        ->where(["fly"=>$companyid])
                        ->asArray()
                        ->all();
                $count = count($child);
                if($count > 0)
                {
                    foreach($child as $k=>$v)
                    {
                        $company[$k] = $v['id'];
                        $company[$k+1] = $companyid;
                    }
                }
                else
                {
                    $company[0] = $companyid;
                }
                $where_company = ['in',"company_categroy_id",$company];
            }
            if($rank == 3)//子公司或者部门经理
            {
                $where_company = ["company_categroy_id"=>$companyid];
            }
            $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
            if(($rank == 4) && in_array("hr",$rules))//部门经理同时是hr
            {
                $where_company = ["company_categroy_id" => $companyid];
            }
            elseif(($rank == 4) && !in_array("hr",$rules))//部门经理非hr
            {
                $where_company = ["department_id" => Yii::$app->user->identity->department_id];
            }

        }
        else
        {
            $where_company = "";
        }
        $userid = User::find()
                ->select('id')
                ->where($where_company)
                ->asArray()
                ->column();
        $query->andFilterWhere([ "in",'user_id',$userid]);
        return $dataProvider;
    }
}
