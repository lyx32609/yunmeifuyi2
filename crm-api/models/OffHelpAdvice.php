<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_help_advice".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $advice
 */
class OffHelpAdvice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_help_advice';
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
            [['user_id', 'type'], 'integer'],
            [['advice'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户id'),
            'type' => Yii::t('app', '反馈的type_id'),
            'advice' => Yii::t('app', '反馈意见'),
            'time' => Yii::t('app', '反馈时间'),
        ];
    }
}
