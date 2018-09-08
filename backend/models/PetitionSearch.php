<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Petition;

/**
 * PetitionSearch represents the model behind the search form about `backend\models\Petition`.
 */
class PetitionSearch extends Petition
{
    public $province;
    public $company_categroy_id;
    public $domain_id;
    public $name;
    public $username;
    public $department_id;
    public $start_time;
    public $end_time;
    public $flag;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'status', 'company_id', 'department_id', 'create_time', 'pass_time'], 'integer'],
            [['title', 'content', 'master_img', 'file', 'ids'], 'safe'],
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
        $query = Petition::find()->leftJoin('off_user','off_user.id=off_petition.uid');
        //->leftJoin('off_examine','off_examine.petition_id=off_petition.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
            'pagesize' => '20',
]
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
            'uid' => $this->uid,
            //'status' => $this->status,
            'off_user.company_categroy_id' => $this->company_id,
            'off_user.department_id' => $this->department_id,
            'create_time' => $this->create_time,
            'pass_time' => $this->pass_time,
            'off_user.username' => $this->username,
            'off_user.name' => $this->name,
        ]);
        $user_id = Yii::$app->user->identity->id;
        if(!((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])))//非总经理或者超级管理员只能看自己的
        {
                $petition = Petition::find()->select(["id"])->where(["uid"=>$user_id])->asArray()->all();
                $examine = Examine::find()->select(["petition_id"])->where(["uid"=>$user_id])->asArray()->all();
                foreach($petition as $v){
                    $ids[] = $v["id"];
                }
                foreach($examine as $v){
                    $ids[] = $v["petition_id"];
                }
                $dataProvider->query->andWhere(['in','off_petition.id',$ids]);

        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'master_img', $this->master_img])
            ->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'ids', $this->ids]);

        return $dataProvider;
    }



}
