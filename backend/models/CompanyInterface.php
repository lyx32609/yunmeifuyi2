<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_company_interface".
 *
 * @property string $id
 * @property string $company_id
 * @property string $url
 * @property string $public_key
 * @property string $privace_key
 * @property string $module_id
 * @property string $createtime
 * @property string $protocol
 */
class CompanyInterface extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_company_interface';
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
            [['company_id', 'url', 'public_key', 'privace_key', 'module_id', 'createtime', 'protocol'], 'required'],
            [['company_id', 'module_id', 'createtime'], 'integer'],
            [['url'], 'string', 'max' => 200],
            [['public_key', 'privace_key'], 'string', 'max' => 80],
            [['protocol'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'company_id' => Yii::t('app', 'Company ID'),
            'url' => Yii::t('app', 'Url'),
            'public_key' => Yii::t('app', 'Public Key'),
            'privace_key' => Yii::t('app', 'Privace Key'),
            'module_id' => Yii::t('app', 'Module ID'),
            'createtime' => Yii::t('app', 'Createtime'),
            'protocol' => Yii::t('app', 'Protocol'),
        ];
    }
}
