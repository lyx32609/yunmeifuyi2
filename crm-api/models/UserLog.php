<?php

namespace app\models;

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
            [['log_title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '关联user表'),
            'type' => Yii::t('app', '数据类型，1 更新采购商坐标记录'),
            'add_time' => Yii::t('app', 'Add Time'),
            'log_title' => Yii::t('app', '操作记录'),
            'log_text' => Yii::t('app', '操作记录明细'),
        ];
    }
}
