<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_shop_note}}".
 *
 * @property string $id
 * @property integer $shop_id
 * @property string $note
 * @property string $time
 * @property string $conte
 * @property string $user
 * @property string $longitude
 * @property string $latitude
 * @property string $imag
 * @property integer $belong
 */
class CompanyShopNote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_shop_note}}';
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
            [['shop_id', 'time', 'belong'], 'integer'],
            [['note', 'conte'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['user'], 'string', 'max' => 50],
            [['imag'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', '商家id'),
            'note' => Yii::t('app', '备注'),
            'time' => Yii::t('app', '时间'),
            'conte' => Yii::t('app', '提交内容'),
            'user' => Yii::t('app', '提交人'),
            'longitude' => Yii::t('app', '提交人经度'),
            'latitude' => Yii::t('app', '提交人纬度'),
            'imag' => Yii::t('app', '图片'),
            'belong' => Yii::t('app', '1 采购商  2代理商'),
        ];
    }
}
