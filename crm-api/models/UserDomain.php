<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_user_domain".
 *
 * @property string $domain_id
 * @property string $agentname
 * @property string $mobile
 * @property string $region
 * @property string $create_time
 * @property string $uid
 * @property string $longitude
 * @property string $latitude
 * @property string $are_region_id
 */
class UserDomain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_domain';
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
            [['create_time', 'uid', 'are_region_id'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['are_region_id'], 'required'],
            [['agentname', 'region'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain_id' => Yii::t('app', 'Domain ID'),
            'agentname' => Yii::t('app', 'Agentname'),
            'mobile' => Yii::t('app', 'Mobile'),
            'region' => Yii::t('app', 'Region'),
            'create_time' => Yii::t('app', 'Create Time'),
            'uid' => Yii::t('app', 'Uid'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'are_region_id' => Yii::t('app', 'Are Region ID'),
        ];
    }
}
