<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "off_regions".
 *
 * @property string $region_id
 * @property string $package
 * @property string $p_region_id
 * @property string $region_path
 * @property string $region_grade
 * @property string $local_name
 * @property string $en_name
 * @property string $p_1
 * @property string $p_2
 * @property string $ordernum
 * @property string $disabled
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_regions';
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
            [['p_region_id', 'region_grade', 'ordernum'], 'integer'],
            [['disabled'], 'string'],
            [['package'], 'string', 'max' => 20],
            [['region_path'], 'string', 'max' => 255],
            [['local_name', 'en_name', 'p_1', 'p_2'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'region_id' => Yii::t('app', 'Region ID'),
            'package' => Yii::t('app', 'Package'),
            'p_region_id' => Yii::t('app', 'P Region ID'),
            'region_path' => Yii::t('app', 'Region Path'),
            'region_grade' => Yii::t('app', 'Region Grade'),
            'local_name' => Yii::t('app', 'Local Name'),
            'en_name' => Yii::t('app', 'En Name'),
            'p_1' => Yii::t('app', 'P 1'),
            'p_2' => Yii::t('app', 'P 2'),
            'ordernum' => Yii::t('app', 'Ordernum'),
            'disabled' => Yii::t('app', 'Disabled'),
        ];
    }
    
    public static function findProvince(){
        return Regions::find()
        ->where( ['region_grade'=>1]);
    }
    
    public static function findCity($province){
        if($province){
            $where = ['p_region_id'=>$province];
        }else{
            $where = '';
        }
        return Regions::find()
        ->where(["region_grade" => 2])
        ->andWhere( $where );
    }
    
    
    
}
