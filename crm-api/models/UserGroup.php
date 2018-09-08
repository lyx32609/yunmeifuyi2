<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_group}}".
 *
 * @property string $id
 * @property string $name
 * @property string $desc
 * @property string $domain_id
 * @property string $priority
 * @property integer $is_select
 * @property string $department_id
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_group}}';
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
            [['domain_id', 'department_id'], 'required'],
            [['domain_id', 'priority', 'is_select', 'department_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 255]
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
            'domain_id' => Yii::t('app', 'Domain ID'),
            'priority' => Yii::t('app', 'Priority'),
            'is_select' => Yii::t('app', 'Is Select'),
            'department_id' => Yii::t('app', 'Department ID'),
        ];
    }

    
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['group_id' => 'id']);
    }
}
