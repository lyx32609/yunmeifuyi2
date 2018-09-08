<?php

namespace backend\models;

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
    public $province;
    public $city;
    public $menuids;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user';
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
            [['staff_code', 'username', 'password', 'domain_id',  'company_categroy_id'], 'required'],
            [['staff_code', 'token_createtime', 'is_select', 'rank',  'is_staff', 'dimission_time'], 'integer'],
            [['username', 'password'], 'string', 'max' => 50],
            [['access_token', 'cid'], 'string', 'max' => 32],
            [['name', 'phone'], 'string', 'max' => 20],
            [['head'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'staff_code' => Yii::t('app', 'Staff Code'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'access_token' => Yii::t('app', 'Access Token'),
            'token_createtime' => Yii::t('app', 'Token Createtime'),
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'head' => Yii::t('app', 'Head'),
            'domain_id' => Yii::t('app', '市'),
            'group_id' => Yii::t('app', 'Group ID'),
            'department_id' => Yii::t('app', 'Department ID'),    
            'is_select' => Yii::t('app', 'Is Select'),
            'rank' => Yii::t('app', 'Rank'),
            'cid' => Yii::t('app', '设备ID号'),
            'company_categroy_id' => Yii::t('app', '公司'),
            'is_staff' => Yii::t('app', '是否在职'),
            'dimission_time' => Yii::t('app', '离职时间'),
            'include_department_id' => Yii::t('app', '所属部门'),
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
        return $this->hasOne(Regions::className(), ['region_id'=>'domain_id']);
    }
}
