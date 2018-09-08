<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_system_record".
 *
 * @property integer $id
 * @property integer $staff_num
 * @property string $content
 * @property integer $type
 * @property string $brand_model
 * @property integer $time
 */
class SystemRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_system_record';
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
            [['staff_num', 'type', 'time'], 'integer'],
            [['brand_model'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'staff_num' => Yii::t('app', '云管理账号'),
            'content' => Yii::t('app', '异常内容'),
            'type' => Yii::t('app', '异常类型'),
            'brand_model' => Yii::t('app', '设备品牌型号'),
            'time' => Yii::t('app', 'Time'),
        ];
    }
}
