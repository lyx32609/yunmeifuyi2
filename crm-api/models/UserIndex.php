<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_user_index".
 *
 * @property integer $id
 * @property integer $userid
 * @property integer $visitingnum
 * @property integer $registernum
 * @property integer $ordernum
 * @property double $orderamount
 * @property integer $orderuser
 * @property double $deposit
 * @property integer $maimaijinorder
 * @property double $maimaijinamount
 * @property integer $maimaijinuser
 * @property integer $inputtime
 */
class UserIndex extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_index';
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
            [['userid', 'visitingnum', 'registernum', 'ordernum', 'orderuser', 'maimaijinorder', 'maimaijinuser', 'inputtime'], 'integer'],
            [['orderamount', 'deposit', 'maimaijinamount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userid' => Yii::t('app', 'Userid'),
            'visitingnum' => Yii::t('app', 'Visitingnum'),
            'registernum' => Yii::t('app', 'Registernum'),
            'ordernum' => Yii::t('app', 'Ordernum'),
            'orderamount' => Yii::t('app', 'Orderamount'),
            'orderuser' => Yii::t('app', 'Orderuser'),
            'deposit' => Yii::t('app', 'Deposit'),
            'maimaijinorder' => Yii::t('app', 'Maimaijinorder'),
            'maimaijinamount' => Yii::t('app', 'Maimaijinamount'),
            'maimaijinuser' => Yii::t('app', 'Maimaijinuser'),
            'inputtime' => Yii::t('app', 'Inputtime'),
        ];
    }
}
