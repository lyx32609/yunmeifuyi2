<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_withd_rate".
 *
 * @property integer $id
 * @property string $pound_money
 * @property integer $pound_percent
 * @property integer $is_open
 * @property string $is_open_which
 */
class WithdRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_withd_rate';
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
            // [['pound_money', 'pound_percent', 'is_open'], 'integer'],
            // [['is_open_which'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pound_money' => 'Pound Money',
            'pound_percent' => 'Pound Percent',
            'is_open' => 'Is Open',
            'is_open_which' => 'Is Open Which',
        ];
    }
}
