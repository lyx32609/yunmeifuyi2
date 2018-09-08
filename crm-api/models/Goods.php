<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $user_name
 * @property string $company_id
 * @property string $orders_money
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $goods_company
 * @property string $goods_num
 * @property string $createtime
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
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
            [['user_id', 'user_name', 'company_id', 'orders_money', 'goods_id', 'goods_name', 'goods_company', 'goods_num', 'createtime'], 'required'],
            [['user_id', 'company_id', 'goods_id', 'goods_num', 'createtime'], 'integer'],
            [['user_name', 'orders_money', 'goods_name', 'goods_company'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户id'),
            'user_name' => Yii::t('app', '用户名'),
            'company_id' => Yii::t('app', '公司id'),
            'orders_money' => Yii::t('app', '订单金额'),
            'goods_id' => Yii::t('app', '商品id'),
            'goods_name' => Yii::t('app', '商品名称'),
            'goods_company' => Yii::t('app', '商品供应商'),
            'goods_num' => Yii::t('app', '商品数量'),
            'createtime' => Yii::t('app', '生成时间'),
        ];
    }
}
