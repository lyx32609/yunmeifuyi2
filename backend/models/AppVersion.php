<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_app_version".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $download
 * @property string $addDate
 * @property integer $type
 * @property string $content
 */
class AppVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_app_version';
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
            [['code', 'name', 'download', 'addDate', 'type', 'content'], 'required'],
            [['code', 'type'], 'integer'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['download', 'addDate'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'download' => Yii::t('app', 'Download'),
            'addDate' => Yii::t('app', 'Add Date'),
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
        ];
    }
}
