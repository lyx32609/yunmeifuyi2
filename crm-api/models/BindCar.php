<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%bind_car}}".
 *
 * @property string $id
 * @property string $user_name
 * @property string $user_id
 * @property string $car_id
 * @property string $car_name
 * @property string $user_phone
 */
class BindCar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bind_car}}';
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
            [['user_name', 'user_id', 'car_id', 'car_name'], 'required'],
            [['user_id'], 'integer'],
            [['user_name', 'car_name', 'user_phone'], 'string', 'max' => 255],
            [['car_id'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_name' => Yii::t('app', '用户名'),
            'user_id' => Yii::t('app', '用户id'),
            'car_id' => Yii::t('app', '车辆id'),
            'car_name' => Yii::t('app', '车辆名称'),
            'user_phone' => Yii::t('app', '用户手机号'),
        ];
    }
}
