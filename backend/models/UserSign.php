<?php

namespace backend\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "off_user_sign".
 *
 * @property string $id
 * @property string $user
 * @property integer $type
 * @property string $time
 * @property string $longitude
 * @property string $latitude
 * @property string $image
 */
class UserSign extends \yii\db\ActiveRecord
{
    public $username;
    public $start_time;
    public $end_time;
    public $area;
    public $city;
    public $department;
    // public $remarks;
    //public $remark;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_sign';
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
            [['user', 'type', 'time'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['image','city'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'type' => Yii::t('app', 'Type'),
            'time' => Yii::t('app', 'Time'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
            'image' => Yii::t('app', 'Image'),
        ];
    }
    public function getUserOne()
    {
        return $this->hasOne(User::className(), ['id'=>'user']);
    }

    public function getImag($img)
    {
        // return  Html::img("http://ngh.crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));
         return  Html::img("http://crm.openapi.xunmall.com/".$img,"",array("width"=>'200px','height'=>'200px'));
    }
}
