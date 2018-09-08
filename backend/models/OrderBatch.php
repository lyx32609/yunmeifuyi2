<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_order_batch".
 *
 * @property string $id
 * @property string $user_id
 * @property string $car_id
 * @property string $car_name
 * @property string $car_driver_name
 * @property string $car_driver_phone
 * @property string $batch_no
 * @property string $batch_wms
 * @property integer $status
 * @property string $start_time
 * @property string $end_time
 */
class OrderBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_order_batch';
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
            [['user_id', 'car_id', 'car_name', 'batch_no', 'batch_wms', 'start_time', 'end_time'], 'required'],
            [['user_id', 'status', 'start_time', 'end_time'], 'integer'],
            [['car_id'], 'string', 'max' => 100],
            [['car_name'], 'string', 'max' => 200],
            [['car_driver_name'], 'string', 'max' => 50],
            [['car_driver_phone'], 'string', 'max' => 20],
            [['batch_no', 'batch_wms'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'car_id' => Yii::t('app', 'Car ID'),
            'car_name' => Yii::t('app', 'Car Name'),
            'car_driver_name' => Yii::t('app', 'Car Driver Name'),
            'car_driver_phone' => Yii::t('app', 'Car Driver Phone'),
            'batch_no' => Yii::t('app', 'Batch No'),
            'batch_wms' => Yii::t('app', 'Batch Wms'),
            'status' => Yii::t('app', 'Status'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
        ];
    }
}
