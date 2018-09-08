<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_indicator_record".
 *
 * @property string $id
 * @property integer $create_time
 * @property string $create_address
 * @property integer $user_id
 * @property string $num
 * @property integer $indicator_id
 */
class IndicatorRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_indicator_record';
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
            [['create_time', 'user_id', 'indicator_id'], 'integer'],
            [['create_address', 'num'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '指标记录ID'),
            'create_time' => Yii::t('app', '指标记录创建时间'),
            'create_address' => Yii::t('app', '创建地址'),
            'user_id' => Yii::t('app', '创建人ID'),
            'num' => Yii::t('app', '指标数量'),
            'indicator_id' => Yii::t('app', '指标ID'),
        ];
    }
}
