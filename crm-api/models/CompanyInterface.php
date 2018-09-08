<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_interface}}".
 *
 * @property string $id
 * @property string $company_id
 * @property string $url
 * @property string $public_key
 * @property string $privace_key
 * @property string $module_id
 * @property string $createtime
 */
class CompanyInterface extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_interface}}';
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
            [['company_id', 'url', 'public_key', 'privace_key', 'module_id', 'createtime'], 'required'],
            [['company_id', 'module_id', 'createtime'], 'integer'],
            [['url'], 'string', 'max' => 200],
            [['public_key', 'privace_key'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'company_id' => Yii::t('app', '对应注册企业的id'),
            'url' => Yii::t('app', '接口地址'),
            'public_key' => Yii::t('app', '公钥'),
            'privace_key' => Yii::t('app', '密钥'),
            'module_id' => Yii::t('app', '功能模块id'),
            'createtime' => Yii::t('app', '添加时间'),
        ];
    }
}
