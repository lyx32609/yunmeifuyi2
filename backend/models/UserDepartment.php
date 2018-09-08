<?php

	namespace backend\models;

use Yii;
use backend\models\Regions;

/**
 * This is the model class for table "{{%user_department}}".
 *
 * @property string $id
 * @property string $name
 * @property string $desc
 * @property string $domain_id
 * @property string $priority
 * @property integer $is_select
 * @property string $company
 * @property string $parent_id
 * @property integer $is_show
 */
class UserDepartment extends \yii\db\ActiveRecord
{
    public $province;
    public $city;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'off_user_department';
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
            [['name', 'domain_id', 'is_select', 'company'], 'required'],
            [['domain_id', 'priority', 'is_select', 'company', 'parent_id', 'is_show'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'desc' => Yii::t('app', 'Desc'),
            'domain_id' => Yii::t('app', 'Domain ID'),
            'priority' => Yii::t('app', 'Priority'),
            'is_select' => Yii::t('app', 'Is Select'),
            'company' => Yii::t('app', 'Company'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'is_show' => Yii::t('app', 'Is Show'),
        ];
    }
    public function getDomain()
    {
        return $this->hasOne(UserDomain::className(), ['domain_id'=>'domain_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['region_id'=>'domain_id']);
    }
    
    
    
    public static function findid($domain_id)
    {
        $company_id = Yii::$app->user->identity->company_categroy_id;
        if($domain_id){
            $where = ['domain_id'=>$domain_id,'company'=>$company_id]; 
        }else{
            $where = ['company'=>$company_id]; 
        }
        return UserDepartment::find()
        ->where($where);
        
    }
    
    
    
    public static function findall($domain_id)
    {
        $company_id = Yii::$app->user->identity->company_categroy_id;
        $where = ['company'=>$company_id]; 
        return UserDepartment::find()
        ->where($where);
    }

    public static function  findDepartment($area_id,$domain_id,$company_id)
    {
        $fly = Yii::$app->user->identity->company_categroy_id;
        //超级管理员(查看所有)
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
            $where = "";
        }
        elseif(Yii::$app->user->identity->rank == 30)//主公司经理(查看主公司及分公司数据)
        {
            $child = CompanyCategroy::find()
                ->select("id")
                ->where(["fly"=>$fly])
                ->asArray()
                ->all();
            if(count($child) > 0)
            {
                foreach($child as $k=>$v)
                {
                    $company[$k] = $v['id'];
                    $company[$k+1] = $fly;
                }
            }
            else
            {
                $company[0] = $fly;
            }
            $where = ["in","company",$company];
        }
        else//其他
        {
            $where = ["company" => $fly];
        }
        if($area_id)
        {
            $city = Regions::find()
                ->select(["region_id"])
                ->where(["p_region_id" => $area_id])
                ->asArray()
                ->all();
            foreach($city as $k=>$v)
            {
                $domain[$k] = $v['region_id'];
            }
            $where_domain = ["in", "domain_id", $domain];
        }
        if($domain_id)
        {
            $where_domain = ["domain_id" => $domain_id];
        }
        else
        {
            $where_domain = "";
        }
        if($company_id)
        {
            $where_company = ["company" => $company_id];
        }
        else
        {
            $where_company = "";
        }
        $department = UserDepartment::find()
                    ->where($where)
                    ->andWhere($where_domain)
                    ->andWhere($where_company)
                    // ->asArray()
                    // ->all()
                    ;
        return $department;
    }
    public static function findTimes(){

        $times = [
            '0' => "全部",
            '1' => '一次',
            '2' => '两次',
            '3' => '三次',
            '4' => '四次',
            '5' => '五次',
        ];
        return $times;

    }
    
    
    
}
