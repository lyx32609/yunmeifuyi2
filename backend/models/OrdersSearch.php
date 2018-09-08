<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Orders;

/**
 * OrdersSearch represents the model behind the search form about `backend\models\Orders`.
 */
class OrdersSearch extends Orders
{
    public $times;
    public $username;
    public $name;
    public $start_time;
    public $end_time;
    public $area;
    public $city;
    public $department;
    public $company;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'createtime', 'finishtime', 'company_id', 'staff_num', 'check_uid'], 'integer'],
            [['check_status', 'pay_status'], 'string'],
            [['check_time', 'pay_time'], 'safe'],
            [['payed', 'status', 'company_name'], 'string', 'max' => 255],
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
        $query = Orders::find()->andWhere($staff_num)->orderBy('finishtime desc');

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
            'order_id' => $this->order_id,
            'createtime' => $this->createtime,
            'finishtime' => $this->finishtime,
            'company_id' => $this->company_id,
            'staff_num' => $this->staff_num,
            'check_status' => $this->check_status,
            'check_time' => $this->check_time,
            'pay_time' => $this->pay_time,
            'pay_status' => $this->pay_status,
        ]);

        $query->andFilterWhere(['like', 'payed', $this->payed])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        return $dataProvider;
    }
}
