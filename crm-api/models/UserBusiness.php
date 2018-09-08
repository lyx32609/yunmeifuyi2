<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_business}}".
 *
 * @property integer $id
 * @property string $customer_name
 * @property string $customer_user
 * @property string $customer_tel
 * @property integer $customer_type
 * @property integer $customer_source
 * @property integer $customer_state
 * @property integer $customer_priority
 * @property string $customer_longitude
 * @property string $customer_latitude
 * @property string $customer_location_name
 * @property string $customer_photo_str
 * @property string $customer_business_title
 * @property string $customer_business_describe
 * @property integer $staff_num
 * @property string $staff_location_name
 * @property int $time
 * @property int $domain_id
 */
class UserBusiness extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_business}}';
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
            [['customer_name', 'customer_tel', 'customer_type', 'customer_source', 'customer_state', 'customer_business_title', 'customer_business_describe', 'staff_num',], 'required'],
            [['staff_num', 'time', 'domain_id'], 'integer'],
            [['customer_type', 'customer_source', 'customer_state', 'customer_priority'], 'string', 'max' => 4],
            [['customer_longitude', 'customer_latitude'], 'number'],
            [['customer_name','customer_user', 'customer_tel', 'customer_business_title'], 'string', 'max' => 50],
            [['customer_photo_str', 'customer_business_describe'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_name' => '客户名称',
            'customer_user' => '用户称呼',
            'customer_tel' => '客户电话',
            'customer_type' => '客户类型',
            'customer_source' => '客户来源',
            'customer_state' => '客户状态',
            'customer_priority' => '客户优先级 1最低  3最高',
            'customer_longitude' => '客户经度',
            'customer_latitude' => '客户纬度',
            'customer_photo_str' => '图片地址',
            'customer_business_title' => '业务标题',
            'customer_business_describe' => '业务描述',
            'staff_num' => '提交人id',
            'time' => '时间',
            'domain_id' => '地域id',
        ];
    }


}
