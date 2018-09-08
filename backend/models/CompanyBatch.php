<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%company_batch}}".
 *
 * @property string $id
 * @property string $type
 * @property string $num
 * @property string $company_id
 */
class CompanyBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_batch}}';
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
            [['type', 'num', 'company_id'], 'required'],
            [['num', 'company_id'], 'integer'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'num' => Yii::t('app', 'Num'),
            'company_id' => Yii::t('app', 'Company ID'),
        ];
    }
}
