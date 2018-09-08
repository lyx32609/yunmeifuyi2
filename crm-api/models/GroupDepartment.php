<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%group_department}}".
 *
 * @property string $id
 * @property string $name
 * @property string $desc
 * @property string $department_id
 * @property string $priority
 * @property integer $is_select
 * @property string $domain_id
 */
class GroupDepartment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group_department}}';
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
            [['name', 'desc', 'department_id', 'domain_id'], 'required'],
            [['department_id', 'priority', 'is_select', 'domain_id'], 'integer'],
            [['name', 'desc'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'desc' => Yii::t('app', 'Desc'),
            'department_id' => Yii::t('app', 'Department ID'),
            'priority' => Yii::t('app', 'Priority'),
            'is_select' => Yii::t('app', 'Is Select'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'is_select'=>Yii::t('app','Is_select'),
        ];
    }

     public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'group_id']);
    }
}
