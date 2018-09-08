<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sdb_members".
 *
 * @property string $member_id
 * @property integer $domain
 * @property string $member_lv_id
 * @property integer $check_status
 * @property string $uname
 * @property string $name
 * @property string $shopname
 * @property string $lastname
 * @property string $firstname
 * @property string $access_token
 * @property string $token_createtime
 * @property string $password
 * @property string $longitude
 * @property string $latitude
 * @property string $business_type
 * @property string $license_code
 * @property string $staff_code
 * @property string $maintainer_staff_code
 * @property string $area
 * @property string $phone
 * @property string $mobile
 * @property string $tel
 * @property string $email
 * @property string $zip
 * @property string $addr
 * @property string $province
 * @property string $city
 * @property string $order_num
 * @property string $refer_id
 * @property string $refer_url
 * @property string $refer_time
 * @property string $c_refer_id
 * @property string $c_refer_url
 * @property string $c_refer_time
 * @property integer $b_year
 * @property integer $b_month
 * @property integer $b_day
 * @property string $sex
 * @property string $addon
 * @property string $wedlock
 * @property string $education
 * @property string $vocation
 * @property string $interest
 * @property string $coin
 * @property string $advance_freeze
 * @property string $point_freeze
 * @property string $point_history
 * @property string $point
 * @property string $score_rate
 * @property string $reg_ip
 * @property string $regtime
 * @property integer $state
 * @property string $pay_time
 * @property string $biz_money
 * @property string $pw_answer
 * @property string $pw_question
 * @property string $fav_tags
 * @property string $custom
 * @property string $cur
 * @property string $lang
 * @property integer $unreadmsg
 * @property string $disabled
 * @property string $remark
 * @property string $remark_type
 * @property integer $login_count
 * @property integer $experience
 * @property string $foreign_id
 * @property string $member_refer
 * @property integer $mail_stack
 * @property string $bank_no
 * @property string $bank_account_name
 * @property integer $bank_type
 * @property string $bank_branch
 * @property string $id_card
 * @property string $certification_id_front
 * @property string $certification_id_back
 * @property string $certification_shop
 * @property string $certification_license
 * @property string $source
 * @property string $shop_coin
 * @property integer $is_collect
 * @property string $advance
 * @property string $pos_merId
 * @property string $pos_termId
 * @property integer $erp
 * @property integer $store_type
 * @property string $virtual_money
 * @property string $icon
 * @property string $certification_thumb
 * @property string $icon_thumb
 * @property string $partner_login_id
 * @property string $rjt_member_deposit
 * @property string $crmid
 */
class Members extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sdb_members';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbmall');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain', 'member_lv_id', 'check_status', 'token_createtime', 'business_type', 'maintainer_staff_code', 'order_num', 'refer_time', 'c_refer_time', 'b_year', 'b_month', 'b_day', 'coin', 'point_freeze', 'point_history', 'point', 'regtime', 'state', 'pay_time', 'unreadmsg', 'login_count', 'experience', 'mail_stack', 'bank_type', 'source', 'shop_coin', 'is_collect', 'erp', 'store_type', 'crmid'], 'integer'],
            [['uname', 'phone', 'email', 'pos_merId', 'pos_termId', 'certification_thumb', 'icon_thumb', 'partner_login_id'], 'required'],
            [['longitude', 'latitude', 'advance_freeze', 'score_rate', 'biz_money', 'advance', 'virtual_money', 'rjt_member_deposit'], 'number'],
            [['sex', 'addon', 'wedlock', 'interest', 'fav_tags', 'custom', 'disabled', 'remark', 'certification_thumb', 'icon_thumb'], 'string'],
            [['uname', 'name', 'lastname', 'firstname', 'refer_id', 'c_refer_id', 'vocation', 'member_refer', 'bank_no', 'bank_account_name', 'id_card'], 'string', 'max' => 50],
            [['shopname', 'area', 'addr', 'foreign_id', 'bank_branch', 'certification_id_front', 'certification_id_back', 'certification_shop', 'certification_license', 'icon'], 'string', 'max' => 255],
            [['access_token', 'password'], 'string', 'max' => 32],
            [['license_code', 'zip', 'province', 'city', 'cur', 'lang'], 'string', 'max' => 20],
            [['staff_code'], 'string', 'max' => 12],
            [['phone', 'mobile', 'tel', 'education'], 'string', 'max' => 30],
            [['email', 'refer_url', 'c_refer_url'], 'string', 'max' => 200],
            [['reg_ip'], 'string', 'max' => 16],
            [['pw_answer', 'pw_question'], 'string', 'max' => 250],
            [['remark_type'], 'string', 'max' => 2],
            [['pos_merId'], 'string', 'max' => 15],
            [['pos_termId'], 'string', 'max' => 8],
            [['partner_login_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => Yii::t('app', 'Member ID'),
            'domain' => Yii::t('app', '域名ID'),
            'member_lv_id' => Yii::t('app', 'Member Lv ID'),
            'check_status' => Yii::t('app', '帐户查核状态，0表示待审核，1表示审核中，2表示已审核 , 3表示审核未通过'),
            'uname' => Yii::t('app', 'Uname'),
            'name' => Yii::t('app', 'Name'),
            'shopname' => Yii::t('app', 'Shopname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'firstname' => Yii::t('app', 'Firstname'),
            'access_token' => Yii::t('app', 'Access Token'),
            'token_createtime' => Yii::t('app', 'Token Createtime'),
            'password' => Yii::t('app', 'Password'),
            'longitude' => Yii::t('app', '经度'),
            'latitude' => Yii::t('app', '纬度'),
            'business_type' => Yii::t('app', '经营类型'),
            'license_code' => Yii::t('app', '营业执照编号'),
            'staff_code' => Yii::t('app', 'Staff Code'),
            'maintainer_staff_code' => Yii::t('app', '维护人员编号'),
            'area' => Yii::t('app', 'Area'),
            'phone' => Yii::t('app', '联系方式'),
            'mobile' => Yii::t('app', 'Mobile'),
            'tel' => Yii::t('app', 'Tel'),
            'email' => Yii::t('app', 'Email'),
            'zip' => Yii::t('app', 'Zip'),
            'addr' => Yii::t('app', 'Addr'),
            'province' => Yii::t('app', 'Province'),
            'city' => Yii::t('app', 'City'),
            'order_num' => Yii::t('app', 'Order Num'),
            'refer_id' => Yii::t('app', 'Refer ID'),
            'refer_url' => Yii::t('app', 'Refer Url'),
            'refer_time' => Yii::t('app', 'Refer Time'),
            'c_refer_id' => Yii::t('app', 'C Refer ID'),
            'c_refer_url' => Yii::t('app', 'C Refer Url'),
            'c_refer_time' => Yii::t('app', 'C Refer Time'),
            'b_year' => Yii::t('app', 'B Year'),
            'b_month' => Yii::t('app', 'B Month'),
            'b_day' => Yii::t('app', 'B Day'),
            'sex' => Yii::t('app', 'Sex'),
            'addon' => Yii::t('app', 'Addon'),
            'wedlock' => Yii::t('app', 'Wedlock'),
            'education' => Yii::t('app', 'Education'),
            'vocation' => Yii::t('app', 'Vocation'),
            'interest' => Yii::t('app', 'Interest'),
            'coin' => Yii::t('app', 'Coin'),
            'advance_freeze' => Yii::t('app', 'Advance Freeze'),
            'point_freeze' => Yii::t('app', 'Point Freeze'),
            'point_history' => Yii::t('app', 'Point History'),
            'point' => Yii::t('app', 'Point'),
            'score_rate' => Yii::t('app', 'Score Rate'),
            'reg_ip' => Yii::t('app', 'Reg Ip'),
            'regtime' => Yii::t('app', 'Regtime'),
            'state' => Yii::t('app', 'State'),
            'pay_time' => Yii::t('app', 'Pay Time'),
            'biz_money' => Yii::t('app', 'Biz Money'),
            'pw_answer' => Yii::t('app', 'Pw Answer'),
            'pw_question' => Yii::t('app', 'Pw Question'),
            'fav_tags' => Yii::t('app', 'Fav Tags'),
            'custom' => Yii::t('app', 'Custom'),
            'cur' => Yii::t('app', 'Cur'),
            'lang' => Yii::t('app', 'Lang'),
            'unreadmsg' => Yii::t('app', 'Unreadmsg'),
            'disabled' => Yii::t('app', 'Disabled'),
            'remark' => Yii::t('app', 'Remark'),
            'remark_type' => Yii::t('app', 'Remark Type'),
            'login_count' => Yii::t('app', 'Login Count'),
            'experience' => Yii::t('app', 'Experience'),
            'foreign_id' => Yii::t('app', 'Foreign ID'),
            'member_refer' => Yii::t('app', 'Member Refer'),
            'mail_stack' => Yii::t('app', 'Mail Stack'),
            'bank_no' => Yii::t('app', '开户帐号'),
            'bank_account_name' => Yii::t('app', '开户人'),
            'bank_type' => Yii::t('app', '1：建设银行，2：工商银行，3：农业银行，4：中国银行，5：招商银行，6：交通银行'),
            'bank_branch' => Yii::t('app', '支行名称'),
            'id_card' => Yii::t('app', 'Id Card'),
            'certification_id_front' => Yii::t('app', '身份证 正面图片'),
            'certification_id_back' => Yii::t('app', '身份证 反面照'),
            'certification_shop' => Yii::t('app', '门头照'),
            'certification_license' => Yii::t('app', '营业执照'),
            'source' => Yii::t('app', '注册来源 0：网站商城，1：手机'),
            'shop_coin' => Yii::t('app', 'Shop Coin'),
            'is_collect' => Yii::t('app', '0：不是采集数据 1：是采集数据'),
            'advance' => Yii::t('app', 'Advance'),
            'pos_merId' => Yii::t('app', 'POS商户号'),
            'pos_termId' => Yii::t('app', 'POS终端号'),
            'erp' => Yii::t('app', '是否启用了ERP系统，1代表启用'),
            'store_type' => Yii::t('app', '店铺类型：0: 普通，1: 直营店，2: 加盟店'),
            'virtual_money' => Yii::t('app', '充值金额'),
            'icon' => Yii::t('app', '用户头像原图'),
            'certification_thumb' => Yii::t('app', 'Certification Thumb'),
            'icon_thumb' => Yii::t('app', 'Icon Thumb'),
            'partner_login_id' => Yii::t('app', '融捷通系统对应采购商编号'),
            'rjt_member_deposit' => Yii::t('app', '融捷通子账户余额'),
            'crmid' => Yii::t('app', 'Crmid'),
        ];
    }
}
