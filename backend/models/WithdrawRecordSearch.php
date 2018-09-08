<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\User;

/**
 * WithdrawRecordSearch represents the model behind the search form about `backend\models\WithdrawRecord`.
 */
class WithdrawRecordSearch extends WithdrawRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time', 'status', 'staff_num', 'flag', 'order_id'], 'integer'],
            [['money', 'service_fee'], 'number'],
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
            $staff_num = "";//可以看到全部的人员
        }
        else {
            $company = Yii::$app->user->identity->company_categroy_id;
            if (!$company) {
                echo "<script>alert('当前登录账号公司id不存在！');history.back()</script>";
                return false;
            }
            $user_data = User::find()
                ->select('username')
                ->where(['company_categroy_id' => $company])
                ->asArray()
                ->all();
            $staff_nums = array_column($user_data, 'username');
//            var_dump($staff_nums);die;
            $staff_num = ["in", "staff_num", $staff_nums];
        }
        $query = WithdrawRecord::find()
            ->andWhere($staff_num)
            ->andWhere(['flag'=>'2'])
            ->orderBy('time desc');
//        $query = WithdrawRecord::find();

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
            'money' => $this->money,
            'time' => $this->time,
            'status' => $this->status,
            'staff_num' => $this->staff_num,
            'flag' => $this->flag,
            'order_id' => $this->order_id,
            'service_fee' => $this->service_fee,
        ]);

        return $dataProvider;
    }
}
