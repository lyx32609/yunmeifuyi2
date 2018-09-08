<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_company_categroy".
 *
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property integer $createtime
 * @property string $phone
 * @property string $area_id
 * @property string $domain_id
 * @property string $fly
 * @property integer $type
 * @property integer $review
 * @property string $license_num
 * @property string $register_money
 * @property string $business
 * @property string $business_ress
 * @property string $staff_num
 * @property string $acting
 * @property integer $proxy_level
 * @property string $service_area
 * @property string $distribution_merchant
 * @property string $distribution_car
 * @property string $distribution_staff
 * @property string $goods_num
 * @property integer $failure
 * @property string $goods_type
 * @property string $service_type
 * @property string $product_type
 * @property string $salas_business
 * @property string $license_image
 * @property string $user_image_negative
 * @property string $user_image_positive
 */
class ChildCompanyCategroy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_company_categroy';
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
            [['name', 'status', 'createtime', 'phone', 'area_id', 'domain_id', 'fly'], 'required'],
            [['status', 'createtime', 'area_id', 'domain_id', 'fly', 'type', 'review', 'staff_num', 'proxy_level', 'goods_num', 'failure', 'goods_type', 'service_type', 'product_type'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 11],
            [['license_num'], 'string', 'max' => 15],
            [['register_money', 'business', 'business_ress', 'acting', 'service_area', 'distribution_merchant', 'distribution_car', 'distribution_staff', 'salas_business', 'license_image', 'user_image_negative', 'user_image_positive'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'createtime' => Yii::t('app', 'Createtime'),
            'phone' => Yii::t('app', 'Phone'),
            'area_id' => Yii::t('app', 'Area ID'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'fly' => Yii::t('app', 'Fly'),
            'type' => Yii::t('app', 'Type'),
            'review' => Yii::t('app', 'Review'),
            'license_num' => Yii::t('app', 'License Num'),
            'register_money' => Yii::t('app', 'Register Money'),
            'business' => Yii::t('app', 'Business'),
            'business_ress' => Yii::t('app', 'Business Ress'),
            'staff_num' => Yii::t('app', 'Staff Num'),
            'acting' => Yii::t('app', 'Acting'),
            'proxy_level' => Yii::t('app', 'Proxy Level'),
            'service_area' => Yii::t('app', 'Service Area'),
            'distribution_merchant' => Yii::t('app', 'Distribution Merchant'),
            'distribution_car' => Yii::t('app', 'Distribution Car'),
            'distribution_staff' => Yii::t('app', 'Distribution Staff'),
            'goods_num' => Yii::t('app', 'Goods Num'),
            'failure' => Yii::t('app', 'Failure'),
            'goods_type' => Yii::t('app', 'Goods Type'),
            'service_type' => Yii::t('app', 'Service Type'),
            'product_type' => Yii::t('app', 'Product Type'),
            'salas_business' => Yii::t('app', 'Salas Business'),
            'license_image' => Yii::t('app', 'License Image'),
            'user_image_negative' => Yii::t('app', 'User Image Negative'),
            'user_image_positive' => Yii::t('app', 'User Image Positive'),
        ];
    }
}
