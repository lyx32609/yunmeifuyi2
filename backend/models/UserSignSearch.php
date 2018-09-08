<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserSign;
use backend\models\User;
use backend\models\CompanyCategroy;

/**
 * UserSignSearch represents the model behind the search form about `backend\models\UserSign`.
 */
class UserSignSearch extends UserSign
{
    public $area;
    public $city;
    public $name;
    public $company_categroy_id;
    public $deparmtment;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user', 'type', 'time'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['image'], 'safe'],
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
        $query = UserSign::find()
        ->from(['setting' => self::tableName()])
        ->select([
            'setting.*',   
            'user.name',
            'user.company_categroy_id',
        ])
        ->leftJoin(['user' => User::tableName()], 'user.id = setting.user');

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
            'user' => $this->user,
            'path' => $this->path,
            'time' => $this->time,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ]);
 
        $id = Yii::$app->user->identity->id;
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
        $query->andFilterWhere([ "in",'user',$userid]);
        return $dataProvider;
    }

    public function getImag($img)
    {
        return  Html::img("http://ngh.crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));
        // return  Html::img(Yii::$app->request->hostInfo."/".$img,"",array("width"=>'200px','height'=>'200px'));
    }
}
