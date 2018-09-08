<?php

namespace backend\models;

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
    public $stime;
    public $etime;
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
            [[ 'visitingnum', 'registernum', 'ordernum', 'orderuser', 'maimaijinorder', 'maimaijinuser', 'inputtime'], 'integer'],
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
            'userid' => Yii::t('app', '用户ID'),
            'visitingnum' => Yii::t('app', '拜访数量'),
            'registernum' => Yii::t('app', '累计注册量，累计自己注册'),
            'ordernum' => Yii::t('app', '累计订单数量'),
            'orderamount' => Yii::t('app', '累计订单金额'),
            'orderuser' => Yii::t('app', '累计订单用户'),
            'deposit' => Yii::t('app', '累计预存款订金额'),
            'maimaijinorder' => Yii::t('app', '累计买买金订单量'),
            'maimaijinamount' => Yii::t('app', '累计买买金订单金额'),
            'maimaijinuser' => Yii::t('app', '累计买买金订单用户量'),
            'inputtime' => Yii::t('app', 'Inputtime'),
        ];
    }
    public function getUserOne()
    {
        return $this->hasOne(User::className(), ['username'=>'userid']);
    }
}
