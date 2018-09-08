<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_bind_count".
 *
 * @property integer $id
 * @property string $local_count
 * @property string $other_count
 * @property integer $local_department
 * @property integer $other_department
 * @property integer $operation_id
 * @property string $operation_content
 * @property integer $time
 */
class BindCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_bind_count';
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
            [['local_department', 'other_department', 'operation_id', 'time'], 'integer'],
            [['local_count', 'other_count', 'operation_content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'local_count' => Yii::t('app', '云管理账号'),
            'other_count' => Yii::t('app', '关联的其他账号'),
            'local_department' => Yii::t('app', '云管理部门ID'),
            'other_department' => Yii::t('app', '关联的其他账号所属部门ID'),
            'operation_id' => Yii::t('app', '操作人ID'),
            'operation_content' => Yii::t('app', '操作内容'),
            'time' => Yii::t('app', '账号关联时间'),
        ];
    }
}
