<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\UserLocation;
use app\models\ProviceCity;

class GetCountLocationService extends Service
{
	/**
	 * 业务定位统计（H5）
	 * @param  [type] $area       [省份]
	 * @param  [type] $city       [城市]
	 * @param  [type] $department [部门]
	 * @param  [type] $startTime  [开始时间]
	 * @param  [type] $endTime    [结束时间]
	 * @return [type]             [description]
	 */
	public function getCountLocation($area, $city, $department, $startTime, $endTime)
	{
		if(!$startTime) {
			$startTime = $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		}
		if(!$endTime) {
			$endTime = 	$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		}
		if(!$area) {
			$this->setError('省份不能为空');
			return false;
		}
		if(!$department) {
			$this->setError('部门不能为空');
			return false;
		}
		if($area == '全国'){
			$department_id = UserDepartment::find()
					->select(['id'])
					->where(['name' => $department])
					->andWhere(['is_show' => 1])
					->asArray()
					->all();
			$user = $this->countAreaUser($department_id);
			if(!$user){
					$this->setError('用户查询失败');
					return false;
				}
			for ($i = 0; $i < count($user) ; $i++) { 
				$user[$i] = $user[$i]['username'];
			}
			$num = $this->selectLocation($user, $startTime, $endTime) ? $this->selectLocation($user, $startTime, $endTime) : 0; 
			$user_num = count($user) - $num;
			$area_all = ProviceCity::find()
						->select(['province_name'])
						->asArray()
						->groupBy(['province_name'])
						->all();
			$result = [
				'num' => $num,
				'notpositioned' => $user_num,
				'province' => $area_all,
			];	
		}
		if($area != '全国'){
			if($city == '全部') {
				$city_id = ProviceCity::find()
						->select(['city_id'])
						->where(['province_name' => $area])
						->asArray()
						->all();
				for($i = 0; $i < count($city_id); $i++) {
					$city_id[$i] = $city_id[$i]['city_id'];
				}
				$department_id = UserDepartment::find()
						->select(['id'])
						->where(['in', 'domain_id', $city_id])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->all();
				if(!$department_id)	{
					$this->setError('该城市无所选部门');
					return false;
				}
				$user = $this->countAreaUser($department_id);

				if(!$user){
					$this->setError('用户查询失败');
					return false;
				}
				$user_name = [];
				for ($i = 0; $i < count($user) ; $i++) { 
					$user_name[$i] = $user[$i]['username'];
				}
				$num = $this->selectLocation($user_name, $startTime, $endTime) ? $this->selectLocation($user_name, $startTime, $endTime) : 0;
				$user_num = count($user) - $num;
				$area_all = ProviceCity::find()
							->select(['city_name'])
							->where(['province_name' => $area])
							->asArray()
							->all();
				$result = [
					'num' => $num,
					'notpositioned' => $user_num,
					'province' => $area_all,
				];	
			}
			if($city != '全部'){
				$city_name = UserDomain::find()
						->select(['domain_id'])
						->where(['region' => $city])
						->asArray()
						->one();
				$department_id = UserDepartment::find()
						->select(['id'])
						->where(['domain_id' => $city_name['domain_id']])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->all();
				if(!$department_id)	{
					$this->setError('该城市无所选部门');
					return false;
				}			
				$user = $this->countAreaUser($department_id);
				if(!$user){
					$this->setError('用户查询失败');
					return false;
				}
				$user_name = [];
				for ($i = 0; $i < count($user) ; $i++) { 
					$user_name[$i] = $user[$i]['username'];
				}
				$num = $this->selectLocation($user_name, $startTime, $endTime) ? $this->selectLocation($user_name, $startTime, $endTime) : 0;
				$user_num = count($user_name) - $num;

				$result = [
					'num' => $num,
					'notpositioned' => $user_num,
					'personnel' => $user,
				];
			}
		}
		return $result;
	}
	//统计员工
	public function countAreaUser($department_id)
	{
		for($i = 0; $i < count($department_id); $i++){
			$list[$i] = $department_id[$i]['id'];
		}
		$user = user::find()
				->select(['id','username','name'])
				->where(['in', 'department_id', $list])
				->asArray()
				->all();
				
		return $user;
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