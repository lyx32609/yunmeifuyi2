<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_location}}".
 *
 * @property string $id
 * @property string $shop_id
 * @property string $bing_id
 * @property string $name
 * @property string $longitude
 * @property string $latitude
 * @property string $user
 * @property string $time
 * @property integer $type
 * @property string $domain
 * @property integer $belong
 * @property string $reasonable
 * @property string $username
 * @property string $user_longitude
 * @property string $user_latitude
 */
class OffUserLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_location}}';
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
            [['shop_id', 'bing_id', 'time', 'type', 'domain', 'belong'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['user', 'time', 'type', 'domain', 'user_longitude', 'user_latitude'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['user'], 'string', 'max' => 255],
            [['reasonable'], 'string', 'max' => 6],
            [['username'], 'string', 'max' => 20],
            [['user_longitude', 'user_latitude'], 'string', 'max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', '店铺id'),
            'bing_id' => Yii::t('app', '关联客户id'),
            'name' => Yii::t('app', '店铺、客户名称'),
            'longitude' => Yii::t('app', '经度'),
            'latitude' => Yii::t('app', '纬度'),
            'user' => Yii::t('app', '业务员'),
            'time' => Yii::t('app', '定位时间'),
            'type' => Yii::t('app', '定位来源 0：业务回访 1：新增业务'),
            'domain' => Yii::t('app', '地区'),
            'belong' => Yii::t('app', '1采购商  2代理商  0默认业务跟进时使用'),
            'reasonable' => Yii::t('app', '1 为合理 2为不合理'),
            'username' => Yii::t('app', '用户名'),
            'user_longitude' => Yii::t('app', '用户定位经度'),
            'user_latitude' => Yii::t('app', '用户定位纬度'),
        ];
    }
}
