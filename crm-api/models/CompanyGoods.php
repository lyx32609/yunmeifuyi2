<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_goods}}".
 *
 * @property string $id
 * @property string $goods_name
 */
class CompanyGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_goods}}';
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
            [['goods_name'], 'required'],
            [['goods_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'goods_name' => Yii::t('app', '商品分类名称'),
        ];
    }
}
