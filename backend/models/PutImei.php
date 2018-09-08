<?php

namespace backend\models;

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

    public $username;
    public $start_time;
    public $end_time;
    public $area;
    public $city;
    public $department;
    public $times;

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
            [['user_id', 'submit_time', 'department_id', 'pass_time', 'status', 'company_categroy_id', 'is_read'], 'integer'],
            [['new_imei_number', 'old_imei_number', 'old_brand', 'old_submit_time', 'new_brand'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'new_imei_number' => '当前手机设备号',
            'submit_time' => '用户提报时间',
            'department_id' => '所属部门ID',
            'old_imei_number' => '原手机设备号',
            'pass_time' => '审核通过时间',
            'status' => 'Status',
            'old_brand' => '原手机品牌',
            'old_submit_time' => '原手机设备提报时间',
            'new_brand' => '当前手机品牌',
            'company_categroy_id' => '所属公司ID',
            'is_read' => 'Is Read',
        ];
    }
}
