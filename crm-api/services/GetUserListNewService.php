<?php

namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\AuthAssignment;
use app\models\UserGroup;
use app\models\UserDepartment;

class GetUserListNewService extends Service
{
	/**
	 * 根据部门获取员工列表
	 * @param  [type] $department [description]
	 * @return [type]             [description]
	 */
	public function getUsertList($department)
	{
		$domain = UserDepartment::find()
				->select(['domain_id'])
				->where(['id' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();

		$result = User::find()
					->select(['id', 'username','password', 'name', 'phone', 'department_id', 'domain_id', 'group_id', 'rank'])
					->where(['department_id' => $department])
					->andWhere(['domain_id' => $domain['domain_id']])
					->orderBy('is_staff desc')
					->asArray()
					->all();
		if(!$result){
			$this->setError('暂无人员信息');
			return false;
		}
		for($i = 0; $i < count($result); $i++){
			$list[$i]['item_name'] = $this->checkLogin($result[$i]['id']);
			if($list[$i]['item_name']){
				$result[$i]['lever'] = 1;
				$result[$i]['item_name'] = $list[$i]['item_name'];
			} else {
				$result[$i]['lever'] = 2;
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