<?php

namespace app\models;

use Yii;
use app\models\Member;
use purchaser\models\Staff;

/**
 * This is the model class for table "{{%shop_note}}".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property string $note
 * @property integer $time
 * @property string $conte
 * @property string $user
 * @property string $longitude
 * @property string $latitude
 * @property string $imag
 */
class ShopNote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_note}}';
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
            [['shop_id', 'time','belong'], 'integer'],
            [['note', 'time'], 'required'],
            [['note', 'conte'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['user'], 'string', 'max' => 50],
            [['imag'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => '商家id',
            'note' => '备注',
            'time' => '时间',
            'conte' => '提交内容',
            'user' => '提交人',
            'longitude' => '提交人经度',
            'latitude' => '提交人纬度',
            'imag' => '图片',
            'belong'=>'归属',
        ];
    }


}
