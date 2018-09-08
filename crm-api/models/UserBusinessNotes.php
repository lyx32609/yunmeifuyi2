<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_business_notes}}".
 *
 * @property string $id
 * @property string $business_id
 * @property string $staff_num
 * @property string $time
 * @property string $followup_text
 */
class UserBusinessNotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_business_notes}}';
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
            [['business_id', 'staff_num', 'time', 'followup_text'], 'required'],
            [['business_id', 'time'], 'integer'],
            [['staff_num'], 'string', 'max' => 255],
            [['followup_text'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'business_id' => Yii::t('app', '业务id'),
            'staff_num' => Yii::t('app', '业务员工号'),
            'time' => Yii::t('app', '记录时间'),
            'followup_text' => Yii::t('app', '业务历史记录'),
        ];
    }
}
