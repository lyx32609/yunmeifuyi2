<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\ProviceCity;

class GetSelectMessageService extends Service
{
	/**
	 * 获取省市部门id
	 * @param [type] $user_id    [用户id]
	 * @param [type] $area       [省份名称]
	 * @param [type] $city       [市级名称]
	 * @param [type] $department [部门名称]
	 */
	public function GetSelectMessage($user_id, $area, $city, $department)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$department) {
			$this->setError('部门不能为空');
			return false;
		} 
		$user = User::find()
				->select(['domain_id', 'department_id', 'rank'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		if(!$user) {
			$this->setError('用户不存在');
			return false;
		}
		if($user['rank'] == 1 || $user['rank'] == 2){
			$this->setError('暂无该功能权限');
			return false;
		}
		if($user['rank'] == 3 ) {
			if($area == '全国'){
				$this->setError('没有查看全国的权限啊');
				return false;
			}
			if($city == '全部'){
				$this->setError('没有查看全部城市的权限');
				return false;
			}
			$city_name = ProviceCity::find()
					->select(['city_id', 'city_name'])
					->where(['city_id' => $user['domain_id']])
					->asArray()
					->one();
			if($city_name['city_name'] != $city) {
				$this->setError('您没有查看该区域的权限');
				return false;
			}
			$area_name = ProviceCity::find()
					->select(['province_id', 'province_name'])
					->where(['city_name' => $city_name['city_name']])
					->asArray()
					->one();
			if($area_name['province_name'] != $area) {
				$this->setError('您没有查看该省份的权限');
				return false;
			}
			$department_name_all = UserDepartment::find()
					->select(['id','name'])
					->where(['domain_id' => $user['domain_id']])
					->andWhere(['is_show' => 1])
					->asArray()
					->all();
			$list = [];
			for($i = 0; $i < count($department_name_all); $i++) {
				$list['department'][$i] = $department_name_all[$i]['name'];
			}
			if(!in_array($department, $list['department'])) {
				$this->setError('你所选得部门不存在');
				return false;
			}
			$department_name = UserDepartment::find()
					->select(['id', 'name'])
					->where(['name' => $department])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
		}
		if($user['rank'] == 30) {
			if($area == '全国') {
				$department_name = UserDepartment::find()
						->select(['id', 'name'])
						->where(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				return $result = [
					'area' => ['province_id' => -1, 'province_name' => '全国'],
					'city' => ['city_id' => -1, 'city_name' => '全国'],
					'department' => $department_name,
				];
			} 
			if($city == '全部') {
				$city_name = [
					 'city_id' => -1, 'city_name' => '全部',
				];
			} else {
				$city_name = ProviceCity::find()
						->select(['city_id', 'city_name'])
						->where(['city_name' => $city])
						->asArray()
						->one();
			}
			$area_name = ProviceCity::find()
						->select(['province_id', 'province_name'])
						->where(['province_name' => $area])
						->asArray()
						->one();
			if(!$area) {
				$area = [
					'province_id' => -1,
					'province_name' => '该省份暂无数据',
				];
			}
			
			if(!$city) {
				$city = [
					'city_id' => -1,
					'city_name' => '该城市暂无数据',
				];
			}
			$department_name = UserDepartment::find()
						->select(['id', 'name'])
						->where(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
			if(!$department_name) {
				$department_name = [
					'id' => -1,
					'name' => '该部门暂无数据',
				];
			}
		}
		if ($user['rank'] == 4){
			if($area == '全国'){
				$this->setError('没有查看全国的权限啊');
				return false;
			}
			if($city == '全部'){
				$this->setError('没有查看全部城市的权限');
				return false;
			}
			
			$city_name = ProviceCity::find()
					->select(['city_id', 'city_name'])
					->where(['city_id' => $user['domain_id']])
					->asArray()
					->one();

			if($city_name['city_name'] != $city) {
				$this->setError('您没有查看该区域的权限');
				return false;
			}
			$department_name = UserDepartment::find()
					->select(['id','name'])
					->where(['id' => $user['department_id']])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			if($department_name['name'] != $department) {
				$this->setError('您没有查看该部门的权限');
				return false;
			}
			$area_name = ProviceCity::find()
					->select(['province_id', 'province_name'])
					->where(['city_name' => $city_name['city_name']])
					->asArray()
					->one();
			if($area_name['province_name'] != $area) {
				$this->setError('您没有查看该省份的权限');
				return false;
			}
			
			

		} 
		return $result = [
			'area' => $area_name,
			'city' => $city_name,
			'department' => $department_name,
		];
	}
}