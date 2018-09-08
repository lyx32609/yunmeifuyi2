<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%shop}}".
 *
 * @property string $id
 * @property string $shop_name
 * @property string $name
 * @property string $phone
 * @property integer $shop_type
 * @property integer $shop_source
 * @property integer $shop_status
 * @property integer $shop_priority
 * @property string $shop_longitude
 * @property string $shop_latitude
 * @property string $shop_image
 * @property string $user_name
 * @property string $user_id
 * @property string $company_category_id
 * @property integer $shop_review
 * @property string $shop_addr
 * @property string $shop_domain
 * @property string $createtime
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop}}';
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
            [['shop_name', 'name', 'phone', 'shop_type', 'shop_source', 'shop_status', 'shop_priority', 'shop_longitude', 'user_name', 'user_id', 'company_category_id', 'shop_review', 'shop_addr', 'shop_domain', 'createtime'], 'required'],
            [['shop_type', 'shop_source', 'shop_status', 'shop_priority', 'user_id', 'company_category_id', 'shop_review', 'shop_domain', 'createtime'], 'integer'],
            [['shop_latitude', 'shop_image'], 'number'],
            [['shop_name', 'name', 'shop_longitude', 'user_name', 'shop_addr'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_name' => Yii::t('app', '客户名称'),
            'name' => Yii::t('app', '联系人'),
            'phone' => Yii::t('app', '联系方式'),
            'shop_type' => Yii::t('app', '客户类型 1生产 2供货 3采购 4配送 5店铺'),
            'shop_source' => Yii::t('app', '客户来源 1开发 2网站 3展会 4介绍 5媒体'),
            'shop_status' => Yii::t('app', '客户状态 1潜在 2意向 3已合作 4无意向'),
            'shop_priority' => Yii::t('app', '客户优先级 '),
            'shop_longitude' => Yii::t('app', '店铺经度'),
            'shop_latitude' => Yii::t('app', '店铺纬度'),
            'shop_image' => Yii::t('app', '店铺照片'),
            'user_name' => Yii::t('app', '业务员姓名'),
            'user_id' => Yii::t('app', '用户id'),
            'company_category_id' => Yii::t('app', '公司id'),
            'shop_review' => Yii::t('app', '1审核中 2审核通过 3审核不通过'),
            'shop_addr' => Yii::t('app', '客户地址'),
            'shop_domain' => Yii::t('app', '店铺所在区域'),
            'createtime' => Yii::t('app', '新增时间'),
            'shop_title' => Yii::t('app', '业务标题'),
            'shop_describe' => Yii::t('app', '业务内容'),
            'is_show' => Yii::t('app', '是否可见'),
        ];
    }
}
