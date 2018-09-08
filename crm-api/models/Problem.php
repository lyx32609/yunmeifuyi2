<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%problem}}".
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
        return '{{%problem}}';
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
            [['user_name', 'area', 'city', 'department'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'problem_id' => Yii::t('app', '今日问题ID'),
            'problem_title' => Yii::t('app', '问题标题'),
            'problem_content' => Yii::t('app', '问题内容'),
            'collaboration_department' => Yii::t('app', '协同部门'),
            'priority' => Yii::t('app', '优先级(1代表1级，2代表2级，3代表3级)'),
            'create_time' => Yii::t('app', ' 创建问题时间'),
            'user_id' => Yii::t('app', '用户ID'),
            'problem_lock' => Yii::t('app', '是否完成'),
            'user_name' => Yii::t('app', '用户名'),
            'update_time' => Yii::t('app', '问题解决时间'),
            'area' => Yii::t('app', '省份名称'),
            'city' => Yii::t('app', '城市名称'),
            'department' => Yii::t('app', '部门名称'),
            'company_id' => Yii::t('app', '公司id'),
        ];
    }
}
