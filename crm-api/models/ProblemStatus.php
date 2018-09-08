<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%problem_status}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $status_id
 * @property integer $status
 * @property string $createtime
 */
class ProblemStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%problem_status}}';
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
            [['user_id', 'status_id', 'status', 'createtime'], 'required'],
            [['user_id', 'status_id', 'status', 'createtime'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'status_id' => Yii::t('app', 'Status ID'),
            'status' => Yii::t('app', 'Status'),
            'createtime' => Yii::t('app', 'Createtime'),
        ];
    }
}
