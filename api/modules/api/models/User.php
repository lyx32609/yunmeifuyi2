<?php

namespace api\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $id
 * @property string $staff_code
 * @property string $username
 * @property string $password
 * @property string $access_token
 * @property string $token_createtime
 * @property string $name
 * @property string $phone
 * @property string $head
 * @property string $domain_id
 * @property string $group_id
 * @property string $department_id
 * @property integer $is_select
 * @property integer $rank
 * @property string $cid
 * @property integer $company_categroy_id
 * @property integer $is_staff
 * @property string $dimission_time
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['staff_code', 'username', 'password', 'domain_id', 'group_id', 'department_id'], 'required'],
            [['staff_code', 'token_createtime', 'domain_id', 'group_id', 'department_id', 'is_select', 'rank', 'company_categroy_id', 'is_staff', 'dimission_time'], 'integer'],
            [['username', 'password'], 'string', 'max' => 50],
            [['access_token', 'cid'], 'string', 'max' => 32],
            [['name', 'phone'], 'string', 'max' => 20],
            [['head'], 'string', 'max' => 255],
            [['username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'staff_code' => Yii::t('app', '用户id'),
            'username' => Yii::t('app', '登录用户名'),
            'password' => Yii::t('app', '密码'),
            'access_token' => Yii::t('app', '访问token'),
            'token_createtime' => Yii::t('app', 'access_token刷新时间'),
            'name' => Yii::t('app', '业务人员名称'),
            'phone' => Yii::t('app', '业务人员联系电话'),
            'head' => Yii::t('app', '用户头像'),
            'domain_id' => Yii::t('app', '区域id'),
            'group_id' => Yii::t('app', '分组ID'),
            'department_id' => Yii::t('app', '部门id'),
            'is_select' => Yii::t('app', '该地区人员是否被统计数据，1统计 0不统计'),
            'rank' => Yii::t('app', '职务级别，1一线员工，3地区经理，30总经理'),
            'cid' => Yii::t('app', '推送使用'),
            'company_categroy_id' => Yii::t('app', '企业ID'),
            'is_staff' => Yii::t('app', '是否是在职员工，1是 0不是'),
            'dimission_time' => Yii::t('app', '记录离职时间'),
        ];
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public function getGroup()
    {
        return $this->hasOne(UserGroup::className(), ['id'=>'group_id']);
    }
    public function getDepartment()
    {
        return $this->hasOne(UserDepartment::className(), ['id'=>'department_id']);
    }
    public function getDomain()
    {
        return $this->hasOne(UserDomain::className(), ['domain_id'=>'domain_id']);
    }
}
