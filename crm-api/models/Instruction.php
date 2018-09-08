<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%instruction}}".
 *
 * @property string $instruction_id
 * @property string $problem_id
 * @property string $author
 * @property string $author_id
 * @property string $group
 * @property string $group_id
 * @property string $instruction_content
 * @property string $create_time
 * @property string $company_id
 */
class Instruction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%instruction}}';
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
            [['problem_id', 'author', 'author_id', 'group', 'group_id', 'instruction_content', 'create_time', 'company_id'], 'required'],
            [['problem_id', 'author_id', 'group_id', 'create_time', 'company_id'], 'integer'],
            [['author', 'group'], 'string', 'max' => 100],
            [['instruction_content'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instruction_id' => Yii::t('app', '指令ID'),
            'problem_id' => Yii::t('app', '问题ID'),
            'author' => Yii::t('app', '发布人'),
            'author_id' => Yii::t('app', '发布人ID'),
            'group' => Yii::t('app', '部门'),
            'group_id' => Yii::t('app', '部门ID'),
            'instruction_content' => Yii::t('app', '指令内容'),
            'create_time' => Yii::t('app', '指令创建时间'),
            'company_id' => Yii::t('app', '公司id'),
        ];
    }
}
