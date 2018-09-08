<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_percentum".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $old_per
 * @property string $new_per
 * @property integer $time
 */
class Percentum extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_percentum';
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
            [['time'], 'integer'],
            [['old_per', 'new_per'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '账号',
            'name' => '姓名',
            'old_per' => '修改前比例',
            'new_per' => '修改后比例',
            'time' => '修改时间',
            'flag' => '是否是当前使用',
            'content' => '操作内容',
            'department_id' => '部门',
        ];
    }
    public static function findDepartment()
    {
        $company_id = Yii::$app->user->identity->company_categroy_id;

        $res = UserDepartment::find()
            ->where(['company'=>$company_id])
            ->asArray();
        return $res;
    }
}
