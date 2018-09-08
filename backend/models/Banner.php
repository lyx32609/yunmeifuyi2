<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_banner".
 *
 * @property integer $id
 * @property integer $start_time
 * @property integer $end_time
 * @property string $images
 * @property integer $is_valid
 */
class Banner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_banner';
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
            [['start_time', 'end_time', 'is_valid'], 'required'],
            [['images'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'images' => '图片',
            'is_valid' => '是否有效',
        ];
    }
}
