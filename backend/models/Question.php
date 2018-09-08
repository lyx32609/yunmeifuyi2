<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_question".
 *
 * @property string $question_id
 * @property string $problem_id
 * @property string $question_content
 * @property string $author_id
 * @property string $author
 * @property string $group
 * @property integer $group_id
 * @property string $create_time
 * @property string $company_id
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_question';
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
            [['problem_id', 'question_content', 'author_id', 'author', 'group', 'group_id', 'create_time', 'company_id'], 'required'],
            [['problem_id', 'author_id', 'group_id', 'create_time', 'company_id'], 'integer'],
            [['question_content'], 'string', 'max' => 2000],
            [['author', 'group'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'problem_id' => 'Problem ID',
            'question_content' => 'Question Content',
            'author_id' => 'Author ID',
            'author' => 'Author',
            'group' => 'Group',
            'group_id' => 'Group ID',
            'create_time' => 'Create Time',
            'company_id' => 'Company ID',
        ];
    }
}
