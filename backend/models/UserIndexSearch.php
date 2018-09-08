<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserIndex;
use backend\models\User;

/**
 * UserIndexSearch represents the model behind the search form about `backend\models\UserIndex`.
 */
class UserIndexSearch extends UserIndex
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userid', 'visitingnum', 'registernum', 'ordernum', 'orderuser', 'maimaijinorder', 'maimaijinuser', 'inputtime'], 'integer'],
            [['orderamount', 'deposit', 'maimaijinamount'], 'number'],
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
        $query = UserIndex::find();

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

/*         $department = UserIndex::find()
        ->select('userid,SUM(visitingnum) as visitingnum,SUM(registernum) as registernum , SUM(ordernum) as ordernum ,SUM(orderamount) as orderamount ,SUM(orderuser) as orderuser,SUM(deposit) as deposit , SUM(maimaijinorder) as maimaijinorder ,SUM(maimaijinamount) as maimaijinamount ,SUM(maimaijinuser) as maimaijinuser')
        ->groupBy('userid')
        ->orderBy('userid asc')
        ->asArray()
        ->all(); */
        
        $query->select('userid,SUM(visitingnum) as visitingnum,SUM(registernum) as registernum , SUM(ordernum) as ordernum ,SUM(orderamount) as orderamount ,SUM(orderuser) as orderuser,SUM(deposit) as deposit , SUM(maimaijinorder) as maimaijinorder ,SUM(maimaijinamount) as maimaijinamount ,SUM(maimaijinuser) as maimaijinuser');
        $query ->groupBy('userid');
        
        
        
        $id = Yii::$app->user->identity->id;
        $companyid=Yii::$app->user->identity->company_categroy_id;
        $isadmin = in_array($id, Yii::$app->params['through']);
        if(!$isadmin){
            $username = User::find()->select('username')->where(['company_categroy_id'=>$companyid])->asArray()->column();
            $query->andFilterWhere([ 'userid'=>$username]);
        }
        
        
        // grid filtering conditions
/*         $query->andFilterWhere([
            'id' => $this->id,
            'userid' => $this->userid,
            'visitingnum' => $this->visitingnum,
            'registernum' => $this->registernum,
            'ordernum' => $this->ordernum,
            'orderamount' => $this->orderamount,
            'orderuser' => $this->orderuser,
            'deposit' => $this->deposit,
            'maimaijinorder' => $this->maimaijinorder,
            'maimaijinamount' => $this->maimaijinamount,
            'maimaijinuser' => $this->maimaijinuser,
            'inputtime' => $this->inputtime,
        ]); */

        return $dataProvider;
    }
    
    
    

}
