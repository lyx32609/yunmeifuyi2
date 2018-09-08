<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_order}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $user_name
 * @property string $order_id
 * @property string $car_id
 * @property string $car_name
 * @property string $order_money
 * @property string $car_bnto
 * @property integer $status
 * @property string $create_time
 * @property string $end_time
 * @property integer $order_pay
 * @property string $company_id
 * @property string $member_name
 */
class CompanyOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_order}}';
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
            [['user_id', 'user_name', 'order_id', 'car_id', 'car_name', 'order_money', 'car_bnto', 'status', 'create_time', 'end_time', 'order_pay', 'company_id', 'member_name'], 'required'],
            [['user_id', 'car_bnto', 'status', 'create_time', 'end_time', 'order_pay', 'company_id'], 'integer'],
            [['user_name', 'order_id', 'car_id', 'car_name', 'order_money', 'member_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户id'),
            'user_name' => Yii::t('app', '用户名'),
            'order_id' => Yii::t('app', '订单号'),
            'car_id' => Yii::t('app', '车辆id'),
            'car_name' => Yii::t('app', '车辆的名字'),
            'order_money' => Yii::t('app', '订单金额'),
            'car_bnto' => Yii::t('app', '批次'),
            'status' => Yii::t('app', '1已发货  2已签收'),
            'create_time' => Yii::t('app', '发货时间'),
            'end_time' => Yii::t('app', '签收时间'),
            'order_pay' => Yii::t('app', '1已收款  2未收款'),
            'company_id' => Yii::t('app', '公司id'),
            'member_name' => Yii::t('app', '店铺名称'),
        ];
    }
}
