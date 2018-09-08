<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "sdb_order_delivery".
 *
 * @property string $id
 * @property string $order_id
 * @property string $user_id
 * @property string $member_id
 * @property string $car_id
 * @property integer $status
 * @property string $scan_time
 * @property string $depart_time
 * @property string $sign_for_time
 * @property string $batch_no
 * @property integer $batch_status
 * @property integer $pay_sign_status
 */
class OrderDelivery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_order_delivery';
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
            [['order_id', 'user_id', 'member_id', 'car_id', 'scan_time', 'depart_time', 'sign_for_time', 'batch_no', 'batch_status'], 'required'],
            [['order_id', 'user_id', 'member_id', 'status', 'scan_time', 'depart_time', 'sign_for_time', 'batch_no', 'batch_status', 'pay_sign_status'], 'integer'],
            [['car_id'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', '订单id'),
            'user_id' => Yii::t('app', '配送人员id '),
            'member_id' => Yii::t('app', 'member_id  采购商id'),
            'car_id' => Yii::t('app', '车辆编号'),
            'status' => Yii::t('app', '状态，0 作废 1 扫码装车   2 已发车  3 已签收   '),
            'scan_time' => Yii::t('app', '扫码发货时间'),
            'depart_time' => Yii::t('app', '发车时间'),
            'sign_for_time' => Yii::t('app', '签收时间'),
            'batch_no' => Yii::t('app', '车次的编号'),
            'batch_status' => Yii::t('app', '车次状态，1正常，2完成'),
            'pay_sign_status' => Yii::t('app', '收款状态  0 不需要   1 待收款  2 已收款'),
        ];
    }

}
