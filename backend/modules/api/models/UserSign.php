<?php

namespace backend\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%user_sign}}".
 *
 * @property string $id
 * @property string $user
 * @property integer $type
 * @property string $time
 * @property string $longitude
 * @property string $latitude
 * @property string $image
 * @property string $path
 * @property integer $company_id
 * @property integer $is_late
 * @property string $is_late_time
 */
class UserSign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_sign}}';
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
            [['user', 'type', 'time', 'company_id', 'is_late'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['image', 'path'], 'string', 'max' => 255],
            [['is_late_time'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', '员工id'),
            'type' => Yii::t('app', '签到情况   1 签到  2 签退'),
            'time' => Yii::t('app', '签到时间'),
            'longitude' => Yii::t('app', '签到经度'),
            'latitude' => Yii::t('app', '签到纬度'),
            'image' => Yii::t('app', '位置图片信息'),
            'path' => Yii::t('app', 'Path'),
            'company_id' => Yii::t('app', '对应公司的id'),
            'is_late' => Yii::t('app', '0正常工作 1迟到  2早退'),
            'is_late_time' => Yii::t('app', '迟到或早退时间'),
        ];
    }
}
