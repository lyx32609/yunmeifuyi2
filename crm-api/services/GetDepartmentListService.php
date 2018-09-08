<?php

namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserDepartment;

class GetDepartmentListService extends Service
{
	/**
	 * HR模块 获取当前登入人所在区域的部门信息
	 * @param  [type] $user_id [用户id]
	 * @return [type]          [description]
	 */
	public function getDepartmentList($user_id)
	{
		if(!$user_id){
			$this->setError('用户id不能为空');
			return false;
		}
		$domain_id = User::find()
				->select(['domain_id', 'rank'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$result = UserDepartment::find()
				->select(['id', 'name'])
				->where(['domain_id' => $domain_id['domain_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		if(!$result){
			$this->setError('该地区暂无部门');
			return false;
		}
		for($i = 0; $i < count($result); $i++){
			$result[$i]['domain_id'] = $domain_id['domain_id'];
		}
		return $result;
	}
}