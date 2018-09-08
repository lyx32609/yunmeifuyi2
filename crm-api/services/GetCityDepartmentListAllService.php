<?php

namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDepartment;
use app\models\UserDomain;
use app\models\Regions;
use app\models\UserGroup;
use app\models\AuthAssignment;

class GetCityDepartmentListAllService extends Service
{
	public function getCityDepartmentList($city, $department,$company_category_id)
	{
		if(!$city){
			$this->setError('城市不能为空');
			return false;
		}
		if(!$company_category_id){
			$this->setError('公司id不能为空');
			return false;
		}
		$domain_id = Regions::find()
				->select(['region_id'])
				->where(['like','local_name' ,$city])
				->asArray()
				->one();
		if(!$domain_id){
			$this->setError('该城市暂未开通');
			return false;
		}
		if(!$department){
			$result = UserDepartment::find()
					->select(['id', 'name'])
					->where(['domain_id' => $domain_id['region_id']])
					->andWhere(['company'=>$company_category_id])
					->andWhere(['is_show' => 1])
					->asArray()
					->all();
			if(!$result){
				$this->setError('暂无部门信息');
				return false;
			}
		} else { 
			$department_id = UserDepartment::find()
					->select(['id','company'])
					->where(['name' => $department])
					->andWhere(['domain_id' => $domain_id['region_id']])
// 					->andWhere(['company'=>$company_category_id])
					->andwhere(['is_show' => 1])
					->asArray()
					->one();
			if(!$department_id){
				$this->setError('暂无该部门信息');
				return false;
			}
			$result = User::find()
					->select(['id', 'username','password', 'name', 'phone', 'group_id', 'domain_id', 'department_id', 'rank', 'company_categroy_id'])
					->where(['domain_id' => $domain_id['region_id']])
					->andWhere(['department_id' => $department_id['id']])
					->andWhere(['is_staff' => 1])
					->asArray()
					->all();
			//print_r($result);exit();
			if(!$result){
				return [
					
						'ret' => 28,
						'msg' => [[
							'department_id' => $department_id['id'],
							'domain_id' => $domain_id['region_id'],
							'company_categroy_id' => $department_id['company']
						]]
						
					];
			}
			$list = [];
			for($i = 0; $i < count($result); $i++){
				$list[$i]['item_name'] = $this->checkLogin($result[$i]['id']);
				if($list[$i]['item_name']){
					$result[$i]['lever'] = 1;
					$result[$i]['item_name'] = $list[$i]['item_name'];
				} else {
					$result[$i]['lever'] = 1;
					$result[$i]['item_name'] = $list[$i]['item_name'];
				}
				if(!$result[$i]['phone'] || $result[$i]['phone'] == 'null'){
					$result[$i]['phone'] = '';
				}
				if(!$result[$i]['group_id'] || $result[$i]['group_id'] == 'null'){
					$result[$i]['group_id'] = '';
					$result[$i]['group'] = [
						'id' => '',
						'name' => '',
					];
				} else {
					$list[$i]['group'] = $this->getGroup($result[$i]['group_id']);
					$result[$i]['group'] = $list[$i]['group'];
				}
				if(!$result[$i]['company_categroy_id']){
					$result[$i]['company_categroy_id'] = '';
				}
			}
		}
		return $result;

	}
	/**
	 * 判断用户是否已经离职
	 * @param  [type] $user_id [用户id]
	 * @return [type]          [description]
	 */
	public function checkLogin($user_id)
	{
		$data = AuthAssignment::find()
				->select(['item_name'])
				->where(['user_id' => $user_id])
				->asArray()
				->one();
		if(!$data){
			return $data['item_name'] = '';
		}
		return $data['item_name'];
	}
	/**
	 * 获取组名
	 * @param  [type] $group_id [组id]
	 * @return [type]           [description]
	 */
	public function getGroup($group_id){
		$data = UserGroup::find()
				->select(['id', 'name'])
				->where(['id' => $group_id])
				->asArray()
				->one();
		if(!$data){
			return $data = [
				'id' => '',
				'name' => '',
			];
		}
		return $data;
	}
}