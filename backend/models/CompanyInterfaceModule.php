<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_company_interface_module".
 *
 * @property integer $id
 * @property integer $interface_id
 * @property string $name
 * @property string $note
 * @property integer $status
 * @property integer $company_id
 */
class CompanyInterfaceModule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_company_interface_module';
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
            [['interface_id', 'status', 'company_id'], 'integer'],
            [['name', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'interface_id' => Yii::t('app', 'Interface ID'),
            'name' => Yii::t('app', 'Name'),
            'note' => Yii::t('app', 'Note'),
            'status' => Yii::t('app', 'Status'),
            'company_id' => Yii::t('app', 'Company ID'),
        ];
    }
}
