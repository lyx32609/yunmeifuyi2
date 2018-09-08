<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_interface_module}}".
 *
 * @property string $id
 * @property string $module_name
 * @property string $createtime
 */
class CompanyInterfaceModule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_interface_module}}';
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
            [['module_name', 'createtime'], 'required'],
            [['createtime'], 'integer'],
            [['module_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'module_name' => Yii::t('app', '功能模块名称'),
            'createtime' => Yii::t('app', '添加时间'),
        ];
    }
}
