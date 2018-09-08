<?php

namespace backend\models;

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
class UserLocation extends \yii\db\ActiveRecord
{
    public $province;
    public $city;
    public $username;
    public $start_time;
    public $end_time;
    public $area;
    public $department;
    public $personalusername;
    public $areaid;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_location';
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
            [['shop_id', 'bing_id', 'user', 'time', 'type', 'domain', 'belong'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['user', 'time', 'type', 'domain'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['reasonable'], 'string', 'max' => 6],
            [['username'], 'string', 'max' => 20],
            [['user_longitude', 'user_latitude'], 'string', 'max' => 13],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', 'Shop ID'),
            'bing_id' => Yii::t('app', 'Bing ID'),
            'name' => Yii::t('app', 'Name'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'user' => Yii::t('app', 'User'),
            'time' => Yii::t('app', 'Time'),
            'type' => Yii::t('app', 'Type'),
            'domain' => Yii::t('app', 'Domain'),
            'belong' => Yii::t('app', 'Belong'),
            'reasonable' => Yii::t('app', 'Reasonable'),
            'username' => Yii::t('app', 'Username'),
            'user_longitude' => Yii::t('app', 'User Longitude'),
            'user_latitude' => Yii::t('app', 'User Latitude'),
        ];
    }
    public function getUserOne()
    {
        return $this->hasOne(User::className(), ['username'=>'user']);
    }
    public function getUserDomain()
    {
        return $this->hasOne(UserDomain::className(), ['domain_id'=>'domain']);
    }
    public function getUserBusiness()
    {
        return $this->hasOne(UserBusiness::className(), ['id'=>'shop_id']);
    }
    public function getUserBusinessNotes()
    {
        return $this->hasOne(UserBusinessNotes::className(), ['business_id'=>'shop_id','staff_num'=>'user']);
    }
    public function getMallMembers()
    {
        return $this->hasOne(Members::className(), ['member_id'=>'shop_id']);
    }
}
