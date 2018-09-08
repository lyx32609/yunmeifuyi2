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
 * @property string $user_name
 * @property integer $problem_lock
 */
class OffProblem extends \yii\db\ActiveRecord
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
            [['problem_title', 'problem_content', 'collaboration_department', 'priority', 'create_time', 'user_id', 'user_name'], 'required'],
            [['priority', 'create_time', 'user_id', 'problem_lock'], 'integer'],
            [['problem_title'], 'string', 'max' => 200],
            [['problem_content'], 'string', 'max' => 2000],
            [['collaboration_department'], 'string', 'max' => 50],
            [['user_name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'problem_id' => Yii::t('app', 'Problem ID'),
            'problem_title' => Yii::t('app', 'Problem Title'),
            'problem_content' => Yii::t('app', 'Problem Content'),
            'collaboration_department' => Yii::t('app', 'Collaboration Department'),
            'priority' => Yii::t('app', 'Priority'),
            'create_time' => Yii::t('app', 'Create Time'),
            'user_id' => Yii::t('app', 'User ID'),
            'user_name' => Yii::t('app', 'User Name'),
            'problem_lock' => Yii::t('app', 'Problem Lock'),
        ];
    }
}
