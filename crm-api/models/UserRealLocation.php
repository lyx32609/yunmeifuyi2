<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_user_real_location".
 *
 * @property string $id
 * @property string $user
 * @property string $longitude
 * @property string $latitude
 * @property string $time
 * @property string $domain_id
 */
class UserRealLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_real_location';
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
            [['user', 'time', 'domain_id'], 'required'],
            [['longitude', 'latitude'], 'number'],
            [['time', 'domain_id'], 'integer'],
            [['user'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'time' => Yii::t('app', 'Time'),
            'domain_id' => Yii::t('app', 'Domain ID'),
        ];
    }
}
