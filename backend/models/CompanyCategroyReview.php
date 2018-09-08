<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_company_categroy_review".
 *
 * @property integer $id
 * @property string $name
 * @property string $status
 * @property string $createtime
 * @property string $phone
 * @property string $area_id
 * @property string $domain_id
 * @property string $fly
 * @property string $type
 * @property string $review
 * @property string $license_num
 * @property string $register_money
 * @property string $business
 * @property string $business_ress
 * @property string $staff_num
 * @property string $acting
 * @property string $proxy_level
 * @property string $service_area
 * @property string $distribution_merchant
 * @property string $distribution_car
 * @property string $distribution_staff
 * @property string $goods_num
 * @property string $failure
 * @property string $goods_type
 * @property string $service_type
 * @property string $product_type
 * @property string $salas_business
 * @property string $license_image
 * @property string $user_image_negative
 * @property string $user_image_positive
 * @property integer $company_id
 */
class CompanyCategroyReview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_company_categroy_review';
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
            [['company_id'], 'integer'],
            [['name', 'status', 'createtime', 'phone', 'area_id', 'domain_id', 'fly', 'type', 'review', 'license_num', 'register_money', 'business', 'business_ress', 'staff_num', 'acting', 'proxy_level', 'service_area', 'distribution_merchant', 'distribution_car', 'distribution_staff', 'goods_num', 'failure', 'goods_type', 'service_type', 'product_type', 'salas_business', 'license_image', 'user_image_negative', 'user_image_positive'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'phone' => 'Phone',
            'area_id' => 'Area ID',
            'domain_id' => 'Domain ID',
            'fly' => 'Fly',
            'type' => 'Type',
            'review' => 'Review',
            'license_num' => 'License Num',
            'register_money' => 'Register Money',
            'business' => 'Business',
            'business_ress' => 'Business Ress',
            'staff_num' => 'Staff Num',
            'acting' => 'Acting',
            'proxy_level' => 'Proxy Level',
            'service_area' => 'Service Area',
            'distribution_merchant' => 'Distribution Merchant',
            'distribution_car' => 'Distribution Car',
            'distribution_staff' => 'Distribution Staff',
            'goods_num' => 'Goods Num',
            'failure' => 'Failure',
            'goods_type' => 'Goods Type',
            'service_type' => 'Service Type',
            'product_type' => 'Product Type',
            'salas_business' => 'Salas Business',
            'license_image' => 'License Image',
            'user_image_negative' => 'User Image Negative',
            'user_image_positive' => 'User Image Positive',
            'company_id' => 'Company ID',
        ];
    }
}
