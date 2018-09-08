<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_order_delivery".
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
            [['car_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'member_id' => Yii::t('app', 'Member ID'),
            'car_id' => Yii::t('app', 'Car ID'),
            'status' => Yii::t('app', 'Status'),
            'scan_time' => Yii::t('app', 'Scan Time'),
            'depart_time' => Yii::t('app', 'Depart Time'),
            'sign_for_time' => Yii::t('app', 'Sign For Time'),
            'batch_no' => Yii::t('app', 'Batch No'),
            'batch_status' => Yii::t('app', 'Batch Status'),
            'pay_sign_status' => Yii::t('app', 'Pay Sign Status'),
        ];
    }
}
