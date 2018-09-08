<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_user_route".
 *
 * @property integer $id
 * @property integer $userid
 * @property string $menuids
 */
class UserRoute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_route';
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
            [['userid'], 'integer'],
            [['menuids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userid' => Yii::t('app', '用户id'),
            'menuids' => Yii::t('app', '关联menu表里面的id'),
        ];
    }
}
