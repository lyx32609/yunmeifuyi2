<?php
namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\CompanyCategroy;
use app\models\UserDepartment;
use app\models\User;
use app\models\Regions;

class GetUserIdOrNameService extends Service
{
	/**
	 * 获取用户名或用户ID
	 * @param  [type] $user_company_id         [登录人企业ID]
	 * @param  [type] $area            [省]
	 * @return [type] $city            [市]
	 * @return [type] $department_name [部门名]
	 * @return [type] $department_id   [部门id]
	 * @return [type] $company_name    [企业名]
	 * @return [type] $company_id      [企业ID]
	 * @return [type] $type            [类型]1为用户名 2为用户ID
	 */
	public function getuserDataByType($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id,$type)
	{
		if($user_company_id == "")
		{
			$this->setError("登录人企业ID不能为空");
			return false;
		}
		if(!$area)
		{
			$this->setError("省不能为空");
			return false;
		}
		if(!$city)
		{
			$this->setError("市不能为空");
			return false;
		}
		if(!($company_name))
		{
			$this->setError("公司不能为空");
			return false;
		}
		if(!($department_name && $department_id))
		{
			$this->setError("部门不能为空");
			return false;
		}
		if($type == "")
		{
			$type = 1;
		}
		$user_company_data = CompanyCategroy::find()
				   ->select(["id"])
				   ->where(["id" => $user_company_id])
				   ->asArray()
				   ->one();
		if(!$user_company_data)
		{
			$this->setError("该用户所在企业不存在");
			return false;
		}
		$result_data = $this->getUserDataByAll($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id);
		//return $result_data;
		if(!$result_data)
		{
			$this->setError("暂无人员数据");
			return false;
		}
		//return $type;
		if($type == 1)
		{
			foreach($result_data as $k=>$v)
			{
				$list[$k] = $result_data[$k]['username'];//返回用户名
			}		
		}
		if($type == 2)
		{
			foreach($result_data as $k=>$v)
			{
				$list[$k] = $result_data[$k]['id'];//返回用户ID
			}
		}
		//return $list;
		if(count($list) > 1)
		{
			$result = join(",",$list);
		}		
		else
		{
			$result = $list[0];
		}
		//return $result;
		if(!$result)
		{
			$this->setError("暂无人员数据");
			return false;
		}
		return $result;
		
	}
	public function getUserDataByAll($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id)
	{	
		if($area == "全国")/*条件是全国时*/
		{
			$user_company_data = CompanyCategroy::find()
							   ->select(["id","fly"])
							   ->where(["id" => $user_company_id])
							   ->asArray()
							   ->one();
			if($user_company_data['fly'] == 0)//找出公司及所有子公司
			{
				$company_list = CompanyCategroy::find()
							  ->select(["id"])
							  ->where(["fly" => $user_company_data['id']])
							  ->asArray()
							  ->all();
				if($company_list)//有子公司
				{
					for($i=1;$i<=count($company_list);$i++)
					{
						$company_arr[0] = $user_company_id;
 						$company_arr[$i] =  $company_list[$i]['id'];
					}
				}
				else{//没有子公司
					$company_arr[0] = $user_company_id;
				}
			}
			else
			{
					$company_arr[0] = $user_company_id;
			}
			//return $company_arr;
			$department_data = UserDepartment::find()
							->select(["id","company"])
							->where(["in","company",$company_arr])
							->andWhere(["name" => $department_name])
							->andWhere(['is_show' => 1])
							->asArray()
							->all();/*登录人所在公司及子公司的部门*/
			if(!$department_data)
			{
				$this->setError("暂无该部门");
				return false;
			}
			foreach($department_data as $k=>$v)
			{
				$department_list[$k] = $department_data[$k]['id'];
			}
			//return $department_list;
			if(count($department_list)>1)
			{
				$department_str = join(",",$department_list);
			}
			else
			{
				$department_str = $department_list[0];
			}
			$result = User::find()
				  ->select(["id" ,"username","department_id","company_categroy_id"])
				  ->where(["in","department_id",$department_str])
				  ->andWhere(["in","company_categroy_id" , $company_arr])
				  ->asArray()
				  ->all();/*根据条件查出所有人*/
		}
		if($area != "全国")
		{
			if($city == "全部")
			{
				$area_data = Regions::find()
						   ->select(["region_id","local_name"])
						   ->where(["local_name" => $area])
						   ->asArray()
						   ->one();
				$city_data = Regions::find()
						   ->select(["region_id"])
						   ->where(["p_region_id" => $area_data["region_id"]])
						   ->asArray()
						   ->all();
						   //return $city_data;
				foreach($city_data as $k=>$v)
				{
					$city_list[$k] = $city_data[$k]['region_id'];
				}
				//return $city_list;
				$user_company_data = CompanyCategroy::find()
								   ->select(["id","fly"])
								   ->where(["id" => $user_company_id])
								   ->asArray()
								   ->one();
				if($user_company_data['fly'] == 0)//找出公司及所有子公司
				{
					$company_list = CompanyCategroy::find()
								  ->select(["id"])
								  ->where(["fly" => $user_company_data['id']])
								  ->asArray()
								  ->all();
					if($company_list)//有子公司
					{
						for($i=1;$i<=count($company_list);$i++)
						{
							$company_arr[0] = $user_company_id;
	 						$company_arr[$i] =  $company_list[$i]['id'];
						}
					}
					else{//没有子公司
						$company_arr[0] = $user_company_id;
					}
				}
				else
				{
						$company_arr[0] = $user_company_id;
				}
				//return $company_arr;
				$department_data = UserDepartment::find()
								 ->select(["id"])
								 ->where(["in","company",$company_arr])
								 ->andWhere(["name" => $department_name])
								 ->andWhere(["in","domain_id",$city_list])
								 ->andWhere(['is_show' => 1])
								 ->asArray()
								 ->all();
				if(!$department_data)
				{
					$this->setError("暂无人员数据");
					return false;
				}
				foreach($department_data as $k=>$v)
				{
					$department_list[$k] = $department_data[$k]['id'];
				}

				if(count($department_list) > 1)
				{
					$department_str = join(",",$department_list);
				}
				else
				{
					$department_str = $department_list[0];
				}
				$result = User::find()
				  		->select(["id" ,"username","department_id"])
				  		->where(["in","department_id",$department_str])
				  		->andWhere(["in","company_categroy_id" , $company_arr])
				  		->asArray()
				  		->all();/*根据条件查出所有人*/
			}
			if($city != "全部")
			{
				$area_data = Regions::find()
						   ->select(["region_id","local_name"])
						   ->where(["local_name" => $area])
						   ->asArray()
						   ->one();
				$city_data = Regions::find()
						   ->select(["region_id"])
						   ->where(["p_region_id" => $area_data["region_id"]])
						   ->andWhere(["local_name" => $city])
						   ->asArray()
						   ->one();
				if($company_name == "全部")
				{
					$user_company_data = CompanyCategroy::find()
									   ->select(["id","fly"])
									   ->where(["id" => $user_company_id])
									   ->asArray()
									   ->one();
									   //return $user_company_data;
					if($user_company_data['fly'] == 0)//找出公司及所有子公司
					{
						$company_list = CompanyCategroy::find()
									  ->select(["id"])
									  ->where(["fly" => $user_company_data['id']])
									  ->asArray()
									  ->all();
									  //return $company_list;
						if($company_list)//有子公司
						{
							for($i=0;$i<count($company_list);$i++)
							{
								//$company_arr[0] = $user_company_id;
		 						$company_arr[$i] =  $company_list[$i]['id'];
		 						$company_arr[$i+1] = $user_company_id;
							}
						}
						else
						{//没有子公司
							$company_arr[0] = $user_company_id;
						}
						//return $company_arr;
					}
					else
					{
							$company_arr[0] = $user_company_id;
					}
					//return $company_arr;
					$department_data = UserDepartment::find()
								 	->select(["id"])
								 	//->where(["in","company",$company_arr])
								 	->andWhere(["name" => $department_name])
									->andWhere(["domain_id" => $city_data['region_id']])
									->andWhere(['is_show' => 1])
								 	->asArray()
								 	->all();
								 	//return $department_data;
					if(!$department_data)
					{
						$this->setError("该地区不存在该部门");
						return false;
					}
					foreach($department_data as $k=>$v)
					{
						$department_list[$k] = $department_data[$k]['id'];
					}

					if(count($department_list) > 1)
					{
						$department_str = join(",",$department_list);
					}
					else
					{
						$department_str = $department_list[0];
					}
					$result = User::find()
					  		->select(["id" ,"username","department_id"])
					  		->where(["in","department_id",$department_str])
					  		->andWhere(["in","company_categroy_id" , $company_arr])
					  		->andWhere(["domain_id" => $city_data["region_id"]])
					  		->asArray()
					  		->all();/*根据条件查出所有人*/
				}
				if($company_name != "全部")
				{
					$company_data = CompanyCategroy::find()
									   ->select(["id","fly"])
									   ->where(["name" => $company_name])
									   ->asArray()
									   ->one();
					if($company_data['fly'] == 0)//找出公司及所有子公司
					{
						$company_list = CompanyCategroy::find()
									  ->select(["id"])
									  ->where(["fly" => $company_data['id']])
									  ->asArray()
									  ->all();
						if($company_list)//有子公司
						{
							for($i=1;$i<=count($company_list);$i++)
							{
								$company_arr[0] = $company_data['id'];
		 						$company_arr[$i] =  $company_list[$i]['id'];
							}
						}
						else{//没有子公司
							$company_arr[0] = $company_data['id'];
						}
					}
					else
					{
							$company_arr[0] = $company_data['id'];
					}
					//return $company_arr;exit();
					$result = User::find()
				  			->select(["id" ,"username","department_id","company_categroy_id"])
				  			->where(["department_id" => $department_id])
				  			->andWhere(["company_categroy_id" => $company_arr])
				  			->andWhere(["domain_id" => $city_data['region_id']])
				  			->asArray()
				  			->all();/*根据条件查出所有人*/
				}
			}
		}
		return $result;
	}
}

