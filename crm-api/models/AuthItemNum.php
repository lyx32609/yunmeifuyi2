<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_item_num".
 *
 * @property integer $id
 * @property string $item_name
 * @property integer $item_num
 */
class AuthItemNum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_item_num';
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
            [['item_num'], 'required'],
            [['item_num'], 'integer'],
            [['item_name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_name' => '角色名称',
            'item_num' => '角色返回值 0管理员 1业务员  2配送员',
        ];
    }

    /**
     * @inheritdoc
     * @return AuthItemNumQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthItemNumQuery(get_called_class());
    }
}
