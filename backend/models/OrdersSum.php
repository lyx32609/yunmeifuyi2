<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_orders_sum".
 *
 * @property integer $id
 * @property string $staff_num
 * @property string $sum
 * @property string $balance
 * @property string $pay_order_sum
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
            [['staff_num', 'sum', 'balance', 'pay_order_sum'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'staff_num' => 'Staff Num',
            'sum' => 'Sum',
            'balance' => 'Balance',
            'pay_order_sum' => 'Pay Order Sum',
        ];
    }
}
