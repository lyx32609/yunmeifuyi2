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
class TomorrowPlan extends \yii\db\ActiveRecord
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
            [['user_id', 'visit_clent', 'register_num', 'register_self', 'register_spread', 'orders_num', 'orders_money', 'create_time', 'specification', 'user_name'], 'required'],
            [['user_id', 'visit_clent', 'register_num', 'register_self', 'register_spread', 'orders_num', 'orders_money', 'create_time', 'specification'], 'integer'],
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
            'plan_id' => Yii::t('app', '计划id'),
            'user_id' => Yii::t('app', '用户ID'),
            'visit_clent' => Yii::t('app', '拜访客户'),
            'register_num' => Yii::t('app', '注册数量'),
            'register_self' => Yii::t('app', '自己注册'),
            'register_spread' => Yii::t('app', '传播注册'),
            'orders_num' => Yii::t('app', '订单数量'),
            'orders_money' => Yii::t('app', '订单金额'),
            'pre_deposit' => Yii::t('app', '预存款'),
            'pre_money' => Yii::t('app', '预存款订单数'),
            'remarks' => Yii::t('app', '心得体会及建议'),
            'create_time' => Yii::t('app', '创建时间'),
            'specification' => Yii::t('app', '1日报，2周报，3月报，4季报，5年'),
            'user_name' => Yii::t('app', '用户名'),
        ];
    }
}
