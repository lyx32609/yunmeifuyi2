<?php

namespace backend\models;
use yii\helpers\Html;

use Yii;

/**
 * This is the model class for table "off_shop_note".
 *
 * @property string $id
 * @property integer $shop_id
 * @property string $note
 * @property string $time
 * @property string $conte
 * @property string $user
 * @property string $longitude
 * @property string $latitude
 * @property string $imag
 * @property integer $belong
 */
class ShopNote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_shop_note';
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
            [['shop_id', 'time', 'belong'], 'integer'],
            [['note', 'conte'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['user'], 'string', 'max' => 50],
            [['imag'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', 'Shop ID'),
            'note' => Yii::t('app', 'Note'),
            'time' => Yii::t('app', 'Time'),
            'conte' => Yii::t('app', 'Conte'),
            'user' => Yii::t('app', 'User'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'imag' => Yii::t('app', 'Imag'),
            'belong' => Yii::t('app', 'Belong'),
            'is_show' => Yii::t('app', '是否可见'),
        ];
    }
     public function getImag($img)
     {
        //return  Html::img("http://ngh.crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));
//         return  Html::img("http://crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));
         return  Html::img("http://dev.crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));//测试服图片地址
     }

    public function getUserOne()
    {
        return $this->hasOne(User::className(), ['username'=>'user']);
    }
}
