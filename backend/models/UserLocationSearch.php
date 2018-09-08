<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserLocation;

/**
 * UserLocationSearch represents the model behind the search form about `backend\models\UserLocation`.
 */
class UserLocationSearch extends UserLocation
{
	public $reasonable;
    public $area;
    public $city;
    public $company_categroy_id;
    public $company_id;
    public $shopname;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shop_id', 'bing_id', 'user', 'time', 'type', 'domain', 'belong', 'reasonable'], 'integer'],
            [['name','mobile','addr'], 'safe'],
            [['longitude', 'latitude'], 'number'],
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
        $dbname =  substr(User::getDb()->dsn,strpos( User::getDb()->dsn,'dbname=') + 7);
         $query = UserLocation::find() 
            ->from(['setting' => $dbname .'.'. self::tableName()])
            ->select([
                'setting.*',
                'user.company_categroy_id',
            ])
            ->leftJoin(['user' => $dbname .'.'. User::tableName()], 'user.username = setting.user');
    //        ->leftJoin(['members' => 'lzk_mall.sdb_members'], 'members.member_id = setting.shop_id'); 
        

        

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
            'shop_id' => $this->shop_id,
            'bing_id' => $this->bing_id,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'user' => $this->user,
            'time' => $this->time,
            'type' => $this->type,
            'domain' => $this->domain,
            'belong' => $this->belong,
            'name' => $this->name,

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
                ->select('username')
                ->where($where_company)
                ->asArray()
                ->column();
        $query->andFilterWhere([ "in",'user',$userid]);
        return $dataProvider;
    }
}
