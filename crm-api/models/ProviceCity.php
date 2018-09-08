<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "off_provice_city".
 *
 * @property string $id
 * @property string $province_id
 * @property string $province_name
 * @property string $city_id
 * @property string $city_name
 * @property string $department_id
 * @property string $department_name
 */
class ProviceCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_provice_city';
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
            [['province_id', 'province_name', 'city_id', 'city_name', 'department_id', 'department_name'], 'required'],
            [['province_id', 'city_id', 'department_id'], 'integer'],
            [['province_name', 'city_name', 'department_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'province_id' => Yii::t('app', 'Province ID'),
            'province_name' => Yii::t('app', 'Province Name'),
            'city_id' => Yii::t('app', 'City ID'),
            'city_name' => Yii::t('app', 'City Name'),
            'department_id' => Yii::t('app', 'Department ID'),
            'department_name' => Yii::t('app', 'Department Name'),
        ];
    }
}
