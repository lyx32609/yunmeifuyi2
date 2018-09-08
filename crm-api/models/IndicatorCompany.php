<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_indicator_company".
 *
 * @property string $id
 * @property integer $indicator_id
 * @property integer $company_id
 */
class IndicatorCompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_indicator_company';
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
            [['indicator_id', 'company_id'], 'required'],
            [['indicator_id', 'company_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '指标、指标记录关联的ID'),
            'indicator_id' => Yii::t('app', '指标ID'),
            'company_id' => Yii::t('app', '企业ID'),
        ];
    }
}
