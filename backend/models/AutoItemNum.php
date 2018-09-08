<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auto_item_num".
 *
 * @property string $id
 * @property string $item_name
 * @property string $item_num
 */
class AutoItemNum extends \yii\db\ActiveRecord
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
            [['item_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_name' => 'Item Name',
            'item_num' => 'Item Num',
        ];
    }
}
