<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_orders_sum".
 *
 * @property integer $id
 * @property integer $staff_num
 * @property string $sum
 */
class OrdersSum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_orders_sum';
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

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'staff_num' => Yii::t('app', 'Staff Num'),
            'sum' => Yii::t('app', 'Sum'),
        ];
    }
}
