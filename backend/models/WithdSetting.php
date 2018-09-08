<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_withd_setting".
 *
 * @property integer $id
 * @property integer $set_uid
 * @property integer $set_before
 * @property integer $set_after
 * @property integer $set_time
 * @property string $set_cont
 */
class WithdSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_withd_setting';
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
            [['set_uid', 'set_time'], 'integer'],
            [['set_cont', 'set_before', 'set_after'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '操作人账号',
            'name' => '操作人姓名',
            'department' => '操作人部门',
            'set_before' => '修改前',
            'set_after' => '修改后',
            'set_time' => '修改时间',
            'set_cont' => '操作内容',
        ];
    }
}
