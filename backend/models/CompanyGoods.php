<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_goods}}".
 *
 * @property integer $id
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
            [['id', 'goods_name'], 'required'],
            [['id'], 'integer'],
            [['goods_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'goods_name' => Yii::t('app', 'Goods Name'),
        ];
    }
}
