<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_department}}".
 *
 * @property string $id
 * @property string $name
 * @property string $desc
 * @property string $domain_id
 * @property string $priority
 * @property integer $is_select
 * @property string $company
 * @property string $parent_id
 * @property integer $is_show
 */
class UserDepartment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_department}}';
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
            [['name', 'domain_id', 'is_select', 'company'], 'required'],
            [['domain_id', 'priority', 'is_select', 'company', 'parent_id', 'is_show'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 255],
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
            'desc' => Yii::t('app', 'Desc'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'priority' => Yii::t('app', 'Priority'),
            'is_select' => Yii::t('app', 'Is Select'),
            'company' => Yii::t('app', 'Company'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'is_show' => Yii::t('app', 'Is Show'),
        ];
    }
    public function getDomain()
    {
        return $this->hasOne(UserDomain::className(), ['domain_id'=>'domain_id']);
    }
    public static function findid($domain_id)
    {
        if($domain_id){
            $where = ['domain_id'=>$domain_id]; 
        }else{
            $where = ''; 
        }
        return UserDepartment::find()
        ->where($where);
        
    }
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['department_id' => 'id']);
    }
}
