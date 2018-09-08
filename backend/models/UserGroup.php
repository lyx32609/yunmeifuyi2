<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_group}}".
 *
 * @property string $id
 * @property string $name
 * @property string $desc
 * @property string $domain_id
 * @property string $priority
 */
class UserGroup extends \yii\db\ActiveRecord
{
    public  $province;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{off_user_group}}';
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
            [['domain_id','priority'], 'required'],
            [['domain_id', 'priority','is_select'], 'integer'],
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
            'name' => Yii::t('app', 'Group Name'),
            'desc' => Yii::t('app', 'Desc'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'priority' => Yii::t('app', 'Priority'),
            'is_select'=>Yii::t('app','Is_select')
        ];
    }
    public function getDomain()
    {
        return $this->hasOne(UserDomain::className(), ['domain_id'=>'domain_id']);
    }
    public function getDepartment()
    {
    	return $this->hasOne(UserDepartment::className(), ['id'=>'department_id']);
    }
    static function getGroupByDepartment($department_id)
    {
        $group = UserGroup::find()
                ->where(["department_id" => $department_id])
                ->orderBy("priority desc")
                ->asArray()
                ->all();
        return $group;
    }
}
