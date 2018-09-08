<?php

namespace backend\models;

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
            [['content', 'brand_model'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'staff_num' => '用户',
            'content' => '异常内容',
            'type' => '类型',
            'brand_model' => '设备品牌型号',
            'time' => '日期时间',
        ];
    }
}
