<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%app_version}}".
 *
 * @property integer $id
 * @property integer $code
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
        return '{{%app_version}}';
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
            [['code', 'name', 'download', 'addDate', 'type'], 'required'],
            [['code', 'type'], 'integer'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['download', 'addDate'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => '版本号',
            'download' => 'Download',
            'addDate' => 'Add Date',
            'type' => 'Type',
            'content' => '更新内容',
        ];
    }


}
