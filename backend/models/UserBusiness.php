<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_business}}".
 *
 * @property string $id
 * @property string $customer_name
 * @property string $customer_tel
 * @property string $customer_type
 * @property string $customer_source
 * @property string $customer_state
 * @property string $customer_priority
 * @property string $customer_longitude
 * @property string $customer_latitude
 * @property string $customer_photo_str
 * @property string $customer_business_title
 * @property string $customer_business_describe
 * @property string $staff_num
 * @property string $time
 * @property string $domain_id
 * @property string $customer_user
 */
class UserBusiness extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_business';
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
            [['customer_name', 'customer_tel', 'customer_photo_str', 'customer_business_title', 'customer_business_describe', 'staff_num', 'time', 'domain_id'], 'required'],
            [['customer_longitude', 'customer_latitude'], 'number'],
            [['staff_num', 'time', 'domain_id'], 'integer'],
            [['customer_name', 'customer_tel', 'customer_business_title', 'customer_user'], 'string', 'max' => 50],
            [['customer_type', 'customer_source', 'customer_state', 'customer_priority'], 'string', 'max' => 4],
            [['customer_photo_str', 'customer_business_describe'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_name' => Yii::t('app', '客户名称'),
            'customer_tel' => Yii::t('app', '客户电话'),
            'customer_type' => Yii::t('app', '客户类型'),
            'customer_source' => Yii::t('app', '客户来源'),
            'customer_state' => Yii::t('app', '客户状态'),
            'customer_priority' => Yii::t('app', '客户优先级 1最低  3最高'),
            'customer_longitude' => Yii::t('app', '经度'),
            'customer_latitude' => Yii::t('app', '纬度'),
            'customer_photo_str' => Yii::t('app', 'Customer Photo Str'),
            'customer_business_title' => Yii::t('app', '业务标题'),
            'customer_business_describe' => Yii::t('app', '业务描述'),
            'staff_num' => Yii::t('app', '账号'),
            'time' => Yii::t('app', 'Time'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'customer_user' => Yii::t('app', '用户称呼'),
        ];
    }
}
