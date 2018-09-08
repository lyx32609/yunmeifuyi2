<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_ios_version".
 *
 * @property string $id
 * @property string $iosDownload
 * @property string $iosForce
 * @property string $iosUpdateMsg
 * @property string $iosVersion
 * @property string $type
 */
class IosVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_ios_version';
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
            [['iosDownload', 'iosUpdateMsg', 'iosVersion', 'type'], 'required'],
            [['iosDownload', 'iosUpdateMsg', 'iosVersion'], 'string', 'max' => 255],
            [['iosForce', 'type'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iosDownload' => Yii::t('app', 'Ios Download'),
            'iosForce' => Yii::t('app', 'Ios Force'),
            'iosUpdateMsg' => Yii::t('app', 'Ios Update Msg'),
            'iosVersion' => Yii::t('app', 'Ios Version'),
            'type' => Yii::t('app', 'Type'),
        ];
    }
}
