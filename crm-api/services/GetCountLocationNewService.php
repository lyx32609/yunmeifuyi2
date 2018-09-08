<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDepartment;
use app\models\UserLocation;
use app\models\Regions;
use app\models\CompanyCategroy;
use app\benben\DateHelper;
use app\services\GetUserIdOrNameService;

class GetCountLocationNewService extends Service
{
	/**
	 * 业务定位统计（H5）
	 * @param  [type] user_company_id   [登录人所在企业ID]
	 * @param  [type] $area             [省份]
	 * @param  [type] $city             [市]
	 * @param  [type] $department_name  [部门名称]
	 * @param  [type] $department_id    [部门ID]
	 * @param  [type] $company_name     [公司名]
	 * @param  [type] $company_id       [公司ID]
	 * @param  [type] $startTime        [开始时间]
	 * @param  [type] $endTime          [结束时间]
	 * @return [type]             [description]
	 */
	public function getCountLocation($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $startTime, $endTime)
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
		if(!$startTime) {
			$startTime = DateHelper::getTodayStartTime();
		}
		if(!$endTime) {
			$endTime = DateHelper::getTodayEndTime();
		}
		$service = GetUserIdOrNameService::instance();
		$user_data = $service->getuserDataByType($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id,1);
		//return $user_data;
		if(!$user_data)
		{
			$this->setError($area.'省'.$city.'市'.$company_name.'公司'.$department_name.'部门'."暂无人员");
			return false;
		}
		$user = explode(",",$user_data);
		$num = $this->selectLocation($user, $startTime, $endTime) ? $this->selectLocation($user, $startTime, $endTime) : 0;

		if($area == '全国')
		{
			$user_num = count($user) - $num;
			$area_all = Regions::find()
						->select(['local_name'])
						->where(["p_region_id" => null])
						->asArray()
						->all();
			foreach ($area_all as $key => $value) {
				$province[$key] =  $value['local_name'];
			}
						
			$result = [
				'num' => $num,
				'notpositioned' => $user_num,
				'province' => $province,
				'city' => "",
				'company' => "",
				'user' => "",
				'child' => $province,
			];	
			return $result;
		}
		if($area != '全国')
		{
			if($city == '全部') 
			{

				$user_num = count($user) - $num;
				//return $user_num;
				$area_data = Regions::find()
						   ->select(["local_name","region_id"])
						   ->where(["local_name" => $area])
						   ->asArray()
						   ->one();
				$city_all = Regions::find()
						->select(['local_name'])
						->where(["p_region_id" => $area_data['region_id']])
						->asArray()
						->all();
				foreach ($city_all as $key => $value)
				{
					$city_list[$key] = $value['local_name'];
				}
				$result = [
					'num' => $num,
					'notpositioned' => $user_num,
					'province' => "",
					'city' => $city_list,
					'company' => "",
					'user' => "",
					'child' => $city_list,
				];	
				return $result;

			}
			if($city !== '全部'){
				if($company_name == "全部")
				{
					$city_data = Regions::find()
							  ->select(["region_id"])
							  ->where(["local_name" => $city])
							  ->one();
					$user_num = count($user) - $num;
					$company_data = CompanyCategroy::find()
								->select(["id","fly","name"])
								->where(["id" => $user_company_id])
								->asArray()
								->one();
					if($company_data['fly'] == 0)
					{
						$company_child = CompanyCategroy::find()
									   ->select(["name","fly","id"])
									   ->where(["fly" => $company_data['id']])
									   ->andWhere(["domain_id" => $city_data["region_id"]])
									   ->asArray()
									   ->all();
						$count_child = count($company_child);
						if( $count_child>=1)
						{
							foreach ($company_child as $key => $value) {
								$company[0][0] = $company_data['name'];
								$company[0][1] = $company_data['id'];
								$company[$key+1][0] = $value['name'];
								$company[$key+1][1] = $value['id'];
							}
							$result =[
								'num' => $num,
								'notpositioned' => $user_num,
								'province' => "",
								'city' => "",
								'company' => $company,
								'user' => "",
								'child' => $company,
								];
						}
						else
						{
							$company[0][0] = $company_data['name'];
							$company[0][1] = $company_data['id'];
							$result =[
								'num' => $num,
								'notpositioned' => $user_num,
								'province' => "",
								'city' => "",
								'company' => $company,
								'user' => "",
								'child' => "",
								];
						}
					}
					else
					{
						$company[0][0] = $company_data['name'];
						$company[0][1] = $company_data['id'];
						$result =[
								'num' => $num,
								'notpositioned' => $user_num,
								'province' => "",
								'city' => "",
								'company' => $company,
								'user' => "",
								'child' => "",
								];
					}

				}
				else
				{
					$result =[
								'num' => $num,
								'notpositioned' => count($user)-$num,
								'province' => "",
								'city' => "",
								'company' => "",
								'user' => $user,
								'child' => $user,
								];
				}
			}
		}
		return $result;
	}
	//查询员工定位
	public function selectLocation($username, $startTime, $endTime)
	{
		
		$data = UserLocation::find()
				->select(['user'])
				->where(['in', 'user', $username])
				->andWhere(['between','time', $startTime, $endTime])
				->groupBy('user')
				->asArray()
				->all();
		return $list = count($data);
	}
}