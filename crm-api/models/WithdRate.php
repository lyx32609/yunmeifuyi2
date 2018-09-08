<?php

namespace app\models;

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
            [['pound_money', 'pound_percent', 'is_open'], 'integer'],
            [['is_open_which'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pound_money' => Yii::t('app', '单笔手续费（金额）'),
            'pound_percent' => Yii::t('app', '单笔手续费（百分比）'),
            'is_open' => Yii::t('app', '是否开启 1是 2否'),
            'is_open_which' => Yii::t('app', 'money（金额） 或percent（百分比）'),
        ];
    }
}
