<?php

namespace backend\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%sign_sn}}".
 *
 * @property integer $id
 * @property string $sn
 */
class SignSn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_sn}}';
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
            [['id', 'sn'], 'required'],
            [['id'], 'integer'],
            [['sn'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sn' => Yii::t('app', '群英设备号'),
        ];
    }
}
