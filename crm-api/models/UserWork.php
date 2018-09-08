<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_work}}".
 *
 * @property integer $id
 * @property string $to_work
 * @property string $go_work
 * @property string $company_id
 * @property integer $status
 */
class UserWork extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_work}}';
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
            [['to_work', 'go_work', 'company_id', 'status'], 'integer'],
            [['company_id', 'status'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '自增id'),
            'to_work' => Yii::t('app', '上班时间'),
            'go_work' => Yii::t('app', '下班时间'),
            'company_id' => Yii::t('app', '公司id'),
            'status' => Yii::t('app', '1上午  2下午'),
        ];
    }
}
