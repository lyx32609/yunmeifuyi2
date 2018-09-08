<?php

namespace api\modules\api\models;

use Yii;

/**
 * This is the model class for table "{{%user_work}}".
 *
 * @property integer $id
 * @property string $morning_to_work
 * @property string $morning_go_work
 * @property string $company_id
 * @property integer $status
 * @property string $create_time
 * @property string $uid
 * @property string $after_to_work
 * @property string $after_go_work
 * @property integer $is_staff
 * @property string $user_name
 */
class UserWorkSign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_work_sign}}';
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
            [['morning_to_work', 'morning_go_work', 'company_id', 'status', 'create_time', 'uid', 'after_to_work', 'after_go_work', 'is_staff'], 'integer'],
            [['company_id', 'status', 'uid', 'after_to_work', 'after_go_work', 'is_staff', 'user_name'], 'required'],
            [['user_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '自增id'),
            'morning_to_work' => Yii::t('app', '上班时间'),
            'morning_go_work' => Yii::t('app', '下班时间'),
            'company_id' => Yii::t('app', '公司id'),
            'status' => Yii::t('app', '是否需要忽略上午下班及下午上班时间 1忽略  2不忽略'),
            'create_time' => Yii::t('app', '设置时间'),
            'uid' => Yii::t('app', '设置人'),
            'after_to_work' => Yii::t('app', '下午上班'),
            'after_go_work' => Yii::t('app', '下午下班'),
            'is_staff' => Yii::t('app', '是否作废 1作废  2正常使用'),
            'user_name' => Yii::t('app', '操作人员姓名'),
        ];
    }
}
