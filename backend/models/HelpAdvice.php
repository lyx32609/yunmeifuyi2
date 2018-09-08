<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_help_advice".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $advice
 * @property integer $time
 */
class HelpAdvice extends \yii\db\ActiveRecord
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
            [['user_id', 'type', 'time'], 'integer'],
            [['advice'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户名',
            'type' => '类型',
            'advice' => '意见',
            'time' => '时间',
        ];
    }
}
