<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tomorrow_plan}}".
 *
 * @property string $plan_id
 * @property string $user_id
 * @property string $visit_clent
 * @property string $register_num
 * @property string $register_self
 * @property string $register_spread
 * @property string $orders_num
 * @property string $orders_money
 * @property string $pre_deposit
 * @property string $pre_money
 * @property string $remarks
 * @property string $create_time
 * @property string $specification
 * @property string $user_name
 */
class OffTomorrowPlan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tomorrow_plan}}';
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
            [['user_id', 'visit_clent', 'register_num', 'register_self', 'register_spread', 'orders_num', 'orders_money', 'pre_deposit', 'pre_money', 'create_time', 'specification', 'user_name'], 'required'],
            [['user_id', 'visit_clent', 'register_num', 'register_self', 'register_spread', 'orders_num', 'orders_money', 'pre_deposit', 'pre_money', 'create_time', 'specification'], 'integer'],
            [['remarks'], 'string', 'max' => 500],
            [['user_name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('app', 'Plan ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'visit_clent' => Yii::t('app', 'Visit Clent'),
            'register_num' => Yii::t('app', 'Register Num'),
            'register_self' => Yii::t('app', 'Register Self'),
            'register_spread' => Yii::t('app', 'Register Spread'),
            'orders_num' => Yii::t('app', 'Orders Num'),
            'orders_money' => Yii::t('app', 'Orders Money'),
            'pre_deposit' => Yii::t('app', 'Pre Deposit'),
            'pre_money' => Yii::t('app', 'Pre Money'),
            'remarks' => Yii::t('app', 'Remarks'),
            'create_time' => Yii::t('app', 'Create Time'),
            'specification' => Yii::t('app', 'Specification'),
            'user_name' => Yii::t('app', 'User Name'),
        ];
    }
}
