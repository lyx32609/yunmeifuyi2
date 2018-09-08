<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_put_imei".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $new_imei_number
 * @property integer $submit_time
 * @property integer $department_id
 * @property string $old_imei_number
 * @property integer $pass_time
 * @property integer $status
 * @property string $old_brand
 * @property string $old_submit_time
 * @property string $new_brand
 * @property integer $company_categroy_id
 * @property integer $is_read
 */
class PutImei extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_put_imei';
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
            [['user_id'], 'required'],
            [['user_id', 'submit_time', 'department_id', 'pass_time', 'status', 'company_categroy_id', 'is_read'], 'integer'],
            [['new_imei_number', 'old_imei_number', 'old_brand', 'old_submit_time', 'new_brand'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'new_imei_number' => Yii::t('app', '现手机设备号'),
            'submit_time' => Yii::t('app', '用户提交时间'),
            'department_id' => Yii::t('app', '部门ID'),
            'old_imei_number' => Yii::t('app', '原手机设备号'),
            'pass_time' => Yii::t('app', '审核通过时间'),
            'status' => Yii::t('app', '审核状态'),
            'old_brand' => Yii::t('app', '原手机品牌'),
            'old_submit_time' => Yii::t('app', '原设备提交时间'),
            'new_brand' => Yii::t('app', '现手机品牌'),
            'company_categroy_id' => Yii::t('app', '公司ID'),
            'is_read' => Yii::t('app', '1未读2已读'),
        ];
    }
}
