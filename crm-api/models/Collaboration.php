<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%collaboration}}".
 *
 * @property string $collaboration_id
 * @property string $author
 * @property string $author_id
 * @property string $group
 * @property integer $group_id
 * @property string $problem_id
 * @property string $collaboration_content
 * @property integer $create_time
 * @property string $company_id
 */
class Collaboration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collaboration}}';
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
            [['author', 'author_id', 'group', 'group_id', 'problem_id', 'collaboration_content', 'create_time', 'company_id'], 'required'],
            [['author_id', 'group_id', 'problem_id', 'create_time', 'company_id'], 'integer'],
            [['author', 'group'], 'string', 'max' => 100],
            [['collaboration_content'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'collaboration_id' => Yii::t('app', '协同ID'),
            'author' => Yii::t('app', '发布人'),
            'author_id' => Yii::t('app', '发布人ID'),
            'group' => Yii::t('app', '协同部门名称'),
            'group_id' => Yii::t('app', '部门ID'),
            'problem_id' => Yii::t('app', '问题ID'),
            'collaboration_content' => Yii::t('app', '协同内容'),
            'create_time' => Yii::t('app', '协同记录创建时间'),
            'company_id' => Yii::t('app', '公司id'),
        ];
    }
}
