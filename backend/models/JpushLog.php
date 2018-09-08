<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_jpush_log".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $time
 * @property string $content
 */
class JpushLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_jpush_log';
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
            [['receive', 'time'], 'integer'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receive' => 'Receive',
            'time' => 'Time',
            'content' => 'Content',
        ];
    }
}
