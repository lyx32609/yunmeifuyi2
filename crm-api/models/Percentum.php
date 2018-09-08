<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_percentum".
 *
 * @property integer $id
 * @property integer $username
 * @property string $old_per
 * @property string $new_per
 * @property integer $time
 * @property integer $flag
 * @property string $content
 * @property integer $department_id
 * @property string $name
 * @property integer $is_open
 * @property integer $open_time
 * @property integer $close_time
 */
class Percentum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_percentum';
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
            [['username', 'time', 'flag', 'department_id', 'is_open', 'open_time', 'close_time'], 'integer'],
            [['old_per', 'new_per', 'content', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'old_per' => Yii::t('app', 'Old Per'),
            'new_per' => Yii::t('app', 'New Per'),
            'time' => Yii::t('app', 'Time'),
            'flag' => Yii::t('app', '1 当前使用的百分比 0 历史记录'),
            'content' => Yii::t('app', 'Content'),
            'department_id' => Yii::t('app', 'Department ID'),
            'name' => Yii::t('app', 'Name'),
            'is_open' => Yii::t('app', 'Is Open'),
            'open_time' => Yii::t('app', 'Open Time'),
            'close_time' => Yii::t('app', 'Close Time'),
        ];
    }
}
