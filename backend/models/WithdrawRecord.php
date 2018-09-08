<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_withdraw_record".
 *
 * @property integer $id
 * @property string $money
 * @property integer $time
 * @property integer $status
 * @property integer $staff_num
 * @property integer $flag
 * @property string $order_id
 * @property string $service_fee
 */
class WithdrawRecord extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    public $username;
    public $department;
    public $name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_withdraw_record';
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
            [['money', 'service_fee'], 'number'],
            [['time', 'status', 'staff_num', 'flag', 'order_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'money' => 'Money',
            'time' => 'Time',
            'status' => 'Status',
            'staff_num' => 'Staff Num',
            'flag' => 'Flag',
            'order_id' => 'Order ID',
            'service_fee' => 'Service Fee',
        ];
    }

    //获取部门
    public static function findDepartment()
    {
        $company = Yii::$app->user->identity->company_categroy_id;
        //如果是超级管员
        if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            $where = "";
        } else {
            $where = ["company"=>$company];
        }
        $department = UserDepartment::find()
            ->where($where);
        return $department;
    }

}
