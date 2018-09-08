<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_user_log".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $type
 * @property string $add_time
 * @property string $log_title
 * @property string $log_text
 */
class UserLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_log';
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
            [['user_id', 'type', 'add_time', 'log_title', 'log_text'], 'required'],
            [['user_id', 'type', 'add_time'], 'integer'],
            [['log_text'], 'string'],
            [['log_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'add_time' => 'Add Time',
            'log_title' => 'Log Title',
            'log_text' => 'Log Text',
        ];
    }
}
