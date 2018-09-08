<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_problem".
 *
 * @property string $problem_id
 * @property string $problem_title
 * @property string $problem_content
 * @property string $collaboration_department
 * @property integer $priority
 * @property string $create_time
 * @property integer $user_id
 * @property integer $problem_lock
 * @property string $user_name
 * @property string $update_time
 * @property string $area
 * @property string $city
 * @property string $department
 * @property string $company_id
 */
class Problem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_problem';
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
            [['problem_title', 'problem_content', 'priority', 'create_time', 'user_id', 'user_name', 'update_time', 'area', 'city', 'department', 'company_id'], 'required'],
            [['priority', 'create_time', 'user_id', 'problem_lock', 'update_time', 'company_id'], 'integer'],
            [['problem_title', 'collaboration_department'], 'string', 'max' => 200],
            [['problem_content'], 'string', 'max' => 2000],
            [['user_name', 'area', 'city', 'department'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'problem_id' => 'Problem ID',
            'problem_title' => 'Problem Title',
            'problem_content' => 'Problem Content',
            'collaboration_department' => 'Collaboration Department',
            'priority' => 'Priority',
            'create_time' => 'Create Time',
            'user_id' => 'User ID',
            'problem_lock' => 'Problem Lock',
            'user_name' => 'User Name',
            'update_time' => 'Update Time',
            'area' => 'Area',
            'city' => 'City',
            'department' => 'Department',
            'company_id' => 'Company ID',
        ];
    }
}
