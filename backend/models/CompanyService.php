<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_service}}".
 *
 * @property integer $id
 * @property string $service_name
 */
class CompanyService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_service}}';
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
            [['id', 'service_name'], 'required'],
            [['id'], 'integer'],
            [['service_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'service_name' => Yii::t('app', 'Service Name'),
        ];
    }
}
