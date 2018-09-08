<?php
namespace app\services;

use app\foundation\Service;
use app\models\Regions;
use app\models\User;
use app\models\UserDepartment;
class GetAreaCityDepartmentListService extends Service
{
	/**
	 * Hr获取省市部门列表
	 * @param  [type] $company_category_id       [当前登录人所属公司id]
	 * @param  [type] $user_id                   [当前登录人id]
	 * @param  [type] $area_id                   [省id]
	 * @param  [type] $city_id                   [市id]
	 */
	public function getList($company_category_id,$user_id)
	{
		if(!$company_category_id){
			$this->setError('公司id不能为空');
			return false;
		}
		if(!$user_id){
			$this->setError('登录人id不能为空');
			return false;
		}
		$area_list = Regions::find()->select(["region_id","local_name"])->where(["p_region_id" => null])->asArray()->all();//省列表
		foreach ($area_list as $key => $value)
		{
			$city_list = Regions::find()->select(["region_id","local_name"])->where(["p_region_id" => $value["region_id"]])->asArray()->all();//市列表
			foreach($city_list as $k=>$v)
			{
				$department_list = UserDepartment::find()->select(["id",'name'])->where(["domain_id"=>$v["region_id"]])->andWhere(["company" => $company_category_id])->asArray()->all();//部门
				$city_list[$k]['department'] = $department_list;
			}
			$area_list[$key]['city'] = $city_list;
		}
		return $area_list;
	}
	
}