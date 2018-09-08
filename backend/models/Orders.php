<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_orders".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $createtime
 * @property integer $finishtime
 * @property integer $company<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_orders".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $createtime
 * @property integer $finishtime
 * @property integer $company_id
 * @property string $payed
 * @property string $status
 * @property string $company_name
 * @property integer $staff_num
 * @property string $check_status
 * @property string $check_time
 * @property integer $check_uid
 * @property string $pay_time
 * @property string $pay_status
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $start_time;
    public $end_time;
    public $type;
    public static function tableName()
    {
        return 'off_orders';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbofficial');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'createtime', 'finishtime', 'company_id', 'staff_num', 'type'], 'integer'],
            [['payed', 'status', 'company_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单号',
            'createtime' => '订单创建时间',
            'finishtime' => '订单完成时间',
            'company_id' => '公司',
            'payed' => '订单金额',
            'status' => 'Status',
            'company_name' => 'Company Name',
            'staff_num' => '账号',
            'type' => 'Type',
            'money' => '提成金额',
            'percent' => '提成比例'
        ];
    }

    public static function findDepartment($user_id)
    {
        $fly = Yii::$app->user->identity->company_categroy_id;
        //超级管理员(查看所有)
        if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            $where = "";
        } elseif (Yii::$app->user->identity->rank == 30)//主公司经理(查看主公司及分公司数据)
        {
            $child = CompanyCategroy::find()
                ->select("id")
                ->where(["fly" => $fly])
                ->asArray()
                ->all();
            if (count($child) > 0) {
                foreach ($child as $k => $v) {
                    $company[$k] = $v['id'];
                    $company[$k + 1] = $fly;
                }
            } else {
                $company[0] = $fly;
            }
            $where = ["in", "company", $company];
        } else//其他
        {
            $where = ["company" => $fly];
        }
        $department = UserDepartment::find()
            ->where($where)
            // ->asArray()
            // ->all()
        ;
        return $department;
    }

    public static function findStatus()
    {

        $times = [
            '0' => "全部",
            '1' => '未审',
            '2' => '已审',
        ];
        return $times;

    }


}
