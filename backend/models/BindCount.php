<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_bind_count".
 *
 * @property integer $id
 * @property string $local_count
 * @property string $other_count
 * @property integer $local_department
 * @property integer $other_department
 * @property integer $operation_id
 * @property string $operation_content
 * @property integer $time
 */
class BindCount extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    public $username;
    public $company_id;
    public $department;
    public $name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_bind_count';
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
            [['local_department', 'other_department', 'operation_id', 'time'], 'integer'],
            [['local_count', 'other_count', 'operation_content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local_count' => '云管理账号',
            'other_count' => '关联账号',
            'local_department' => '部门',
            'other_department' => '关联部门',
            'operation_id' => '操作人',
            'operation_content' => '操作内容',
            'time' => '关联时间',
        ];
    }

    //获取部门
    public static function findDepartment()
    {
        $company = Yii::$app->user->identity->company_categroy_id;
        //如果是超级管员
        if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            $where = "";
        } else {
            $where = ["company"=>$company];
        }
        $department = UserDepartment::find()
            ->where($where);
        return $department;
    }


}
