<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%ios_version}}".
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
        return '{{%ios_version}}';
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
            [['iosForce', 'type'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iosDownload' => Yii::t('app', '更新地址'),
            'iosForce' => Yii::t('app', '0不强制更新， 1强制更新'),
            'iosUpdateMsg' => Yii::t('app', '更新内容'),
            'iosVersion' => Yii::t('app', '版本号'),
            'type' => Yii::t('app', '0不更新  1更新'),
        ];
    }
}
