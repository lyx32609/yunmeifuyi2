<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_examine".
 *
 * @property integer $id
 * @property integer $petition_id
 * @property integer $uid
 * @property string $status
 * @property string $advice
 * @property integer $examine_time
 * @property integer $is_visible
 */
class Examine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_examine';
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
            [['petition_id', 'uid', 'examine_time', 'is_visible'], 'integer'],
            [['status', 'advice'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'petition_id' => '签呈ID',
            'uid' => 'Uid',
            'status' => '状态',
            'advice' => '意见',
            'examine_time' => '审批时间',
            'is_visible' => '是否可见',
        ];
    }
}
