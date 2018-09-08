<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_help_comment".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $content_id
 * @property integer $type
 */
class OffHelpComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_help_comment';
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
            [['user_id', 'content_id', 'type'], 'integer']
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
            'content_id' => Yii::t('app', '详情对应的id'),
            'type' => Yii::t('app', '有用1 没用2'),
            'time' => Yii::t('app', '评价时间'),
        ];
    }
}
