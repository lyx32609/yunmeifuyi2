<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_indicator".
 *
 * @property string $id
 * @property string $indicator_name
 */
class Indicator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_indicator';
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
            [['indicator_name'], 'required'],
            [['indicator_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '业务指标ID'),
            'indicator_name' => Yii::t('app', '指标名字'),
        ];
    }
}
