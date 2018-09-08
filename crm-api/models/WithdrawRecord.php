<?php

namespace app\models;

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
 */
class WithdrawRecord extends \yii\db\ActiveRecord
{
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
            [['money'], 'number'],
            [['time', 'status', 'staff_num', 'flag'], 'integer'],
            [['order_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'money' => Yii::t('app', 'Money'),
            'time' => Yii::t('app', 'Time'),
            'status' => Yii::t('app', 'Status'),
            'staff_num' => Yii::t('app', 'Staff Num'),
            'flag' => Yii::t('app', '1转账2提现'),
            'order_id' => Yii::t('app', 'Order ID'),
        ];
    }

}
