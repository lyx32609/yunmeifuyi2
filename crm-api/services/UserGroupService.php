<?php
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 上午 11:30
 */
namespace app\services;
use app\foundation\Service;
use app\models\User;
use app\models\UserGroup;
use app\models\Regions;
use app\models\UserDomain;
use app\models\UserDepartment;

class UserGroupService extends Service
{
    /**
     * 获取分组信息列表（新）
     * @param  [type] $area       [省份]
     * @param  [type] $city       [城市]
     * @param  [type] $department [部门]
     * @return [type]             [description]
     */
    public function getUserGroup($area,$city,$department)
    {
        if(!$area){
            $this->setError('省份不能为空');
            return false;
        }
        if(!$city){
            $this->setError('城市不能为空');
            return false;
        }
        if(!$department){
            $this->setError('部门不能为空');
            return false;
        }
        $province_data = Regions::find()
                ->select (['region_id'])
                ->where(['like','local_name',$area])
                ->one();//获取省的ID
        $province_id = $province_data['region_id'];
        $city_data = Regions::find()
                ->select (['region_id'])
                ->where(['like','local_name',$city])
                ->andWhere(['p_region_id' => $province_id])
                ->one();//获取市的ID
        $city_id = $city_data['region_id'];
        $domain = UserDomain::find()
                ->select(["domain_id"])
                ->where(['like','region',$city])
                ->andWhere(['are_region_id' => $city_id])
                ->one();
        $domain_id = $domain['domain_id'];//获取区域的ID
        $department_data = UserDepartment::find()
                ->select(['id','name','domain_id'])
                ->where(['domain_id' => $domain_id])
                ->andWhere(['is_show' => 1])
                ->andWhere(["name" => $department])
                ->one();
        $department_id = $department_data['id'];
        $group_data = UserGroup::find()//根据部门ID查出分组
            ->select(["id","name"])
            ->where(["department_id"=>$department_id])
            ->asArray()
            ->all();
        if(!$group_data)//当为空时返回100
        {
            $this->setError('没有分组信息');
            return false;
        }
            return $group_data;
    }
    /**
     * 获取分组信息列表（改版后）
     * @param  [type] $area       [省份]
     * @param  [type] $city       [城市]
     * @param  [type] $department [部门]
     * @return [type]             [description]
     */
    public function getUserGroupNew($department)
    {
        
        if(!$department){
            $this->setError('部门id不能为空');
            return false;
        }
        $group_data = UserGroup::find()//根据部门ID查出分组
                ->select(["id","name"])
                ->where(["department_id"=>$department_id])
                ->asArray()
                ->all();
        if(!$group_data)//当为空时返回100
        {
            $this->setError('没有分组信息');
            return false;
        }
        return $group_data;
    }
}