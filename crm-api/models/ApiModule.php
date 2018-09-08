<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%api_module}}".
 *
 * @property string $name
 * @property string $label
 *
 * @property Api[] $apis
 */
class ApiModule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{ym_api_module}}';
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
            [['name', 'label'], 'required'],
            [['name', 'label'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'label' => Yii::t('app', 'Label'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApis()
    {
        return $this->hasMany(Api::className(), ['module_id' => 'name']);
    }
    
    /**
     * @return array
     */
    public static function loadOptions()
    {
        $options = [];
        $rows = self::find()->all();
    
        foreach ($rows as $row)
        {
            $options[$row['name']] = $row['label'];
        }
    
        return $options;
    }
}
