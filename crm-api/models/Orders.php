<?php

namespace app\models;

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
 * @property integer $type
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
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
            [['payed', 'status', 'company_name'], 'string', 'max' => 255]
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
            'createtime' => Yii::t('app', 'Createtime'),
            'finishtime' => Yii::t('app', 'Finishtime'),
            'company_id' => Yii::t('app', 'Company ID'),
            'payed' => Yii::t('app', 'Payed'),
            'status' => Yii::t('app', 'Status'),
            'company_name' => Yii::t('app', 'Company Name'),
            'staff_num' => Yii::t('app', 'Staff Num'),
            'type' => Yii::t('app', 'Type'),
        ];
    }
}
