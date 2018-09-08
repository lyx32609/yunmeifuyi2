<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%order_log}}".
 *
 * @property integer $log_id
 * @property string $order_id
 * @property integer $op_id
 * @property string $op_name
 * @property string $log_text
 * @property integer $acttime
 * @property string $behavior
 * @property string $result
 */
class OrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_log}}';
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
            [['order_id', 'op_id', 'acttime'], 'integer'],
            [['log_text', 'result'], 'string'],
            [['op_name'], 'string', 'max' => 30],
            [['behavior'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'order_id' => 'Order ID',
            'op_id' => 'Op ID',
            'op_name' => 'Op Name',
            'log_text' => 'Log Text',
            'acttime' => 'Acttime',
            'behavior' => 'Behavior',
            'result' => 'Result',
        ];
    }

  
}
