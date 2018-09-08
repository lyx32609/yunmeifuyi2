<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "ym_api".
 *
 * @property string $id
 * @property string $name
 * @property string $label
 * @property string $group_id
 * @property string $module_id
 * @property integer $need_login
 * @property integer $version
 * @property integer $publish
 * @property integer $method
 * @property string $platforms
 * @property string $priority
 * @property string $description
 * @property string $example
 * @property string $response
 * @property integer $old
 *
 * @property YmApiModule $module
 * @property YmApiGroup $group
 */
class Api extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ym_api';
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
            [['name', 'label', 'module_id'], 'required'],
            [['group_id', 'need_login', 'version', 'publish', 'method', 'platforms', 'priority', 'old'], 'integer'],
            [['example', 'response'], 'string'],
            [['name'], 'string', 'max' => 60],
            [['label'], 'string', 'max' => 100],
            [['module_id'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => YmApiModule::className(), 'targetAttribute' => ['module_id' => 'name']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => YmApiGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'label' => 'Label',
            'group_id' => 'Group ID',
            'module_id' => 'Module ID',
            'need_login' => 'Need Login',
            'version' => 'Version',
            'publish' => 'Publish',
            'method' => 'Method',
            'platforms' => 'Platforms',
            'priority' => 'Priority',
            'description' => 'Description',
            'example' => 'Example',
            'response' => 'Response',
            'old' => 'Old',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(YmApiModule::className(), ['name' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(YmApiGroup::className(), ['id' => 'group_id']);
    }
}
