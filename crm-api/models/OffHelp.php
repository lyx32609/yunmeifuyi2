<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_help".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $parent_id
 * @property integer $son_id
 * @property string $content
 * @property integer $sumup
 * @property integer $sumdown
 */
class OffHelp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_help';
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
            [['type', 'parent_id', 'son_id', 'sumup', 'sumdown'], 'integer'],
            [['content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', '类型1注意事项2使用须知'),
            'parent_id' => Yii::t('app', '二级列表，对应注意事项和使用须知 id'),
            'son_id' => Yii::t('app', '三级详情，对应列表的id'),
            'content' => Yii::t('app', '内容'),
            'sumup' => Yii::t('app', '有用总和'),
            'sumdown' => Yii::t('app', '没用总和'),
        ];
    }
}
