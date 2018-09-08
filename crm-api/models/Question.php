<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%question}}".
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
        return '{{%question}}';
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
            [['author', 'group'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => Yii::t('app', '补充问题ID'),
            'problem_id' => Yii::t('app', '问题ID'),
            'question_content' => Yii::t('app', '补充问题内容'),
            'author_id' => Yii::t('app', '发布人ID'),
            'author' => Yii::t('app', '发布人'),
            'group' => Yii::t('app', '部门'),
            'group_id' => Yii::t('app', '部门ID'),
            'create_time' => Yii::t('app', '创建时间'),
            'company_id' => Yii::t('app', '公司id'),
        ];
    }
}
