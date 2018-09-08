<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BindCount;

/**
 * BindCountSearch represents the model behind the search form about `backend\models\BindCount`.
 */
class BindCountSearch extends BindCount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'local_department', 'other_department', 'operation_id', 'time'], 'integer'],
            [['local_count', 'other_count', 'operation_content'], 'safe'],
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
        //判断当前登录账号是否为超管
        if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            $department = "";//可以看到全部的部门
        } else {
            $company = Yii::$app->user->identity->company_categroy_id;
            if (!$company) {
                echo "<script>alert('当前登录账号公司id不存在！');history.back()</script>";
                return false;
            }
            $department_data = UserDepartment::find()
                ->select('id')
                ->where(['company' => $company])
                ->asArray()
                ->all();
            $department_ids = array_column($department_data, 'id');
//                var_dump(array_column($department_data,'id'));die;
            $department = ["in", "local_department", $department_ids];
        }
        $query = BindCount::find()->andWhere($department)->orderBy('time desc');
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
            'local_department' => $this->local_department,
            'other_department' => $this->other_department,
            'operation_id' => $this->operation_id,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'local_count', $this->local_count])
            ->andFilterWhere(['like', 'other_count', $this->other_count])
            ->andFilterWhere(['like', 'operation_content', $this->operation_content]);

        return $dataProvider;
    }
}
