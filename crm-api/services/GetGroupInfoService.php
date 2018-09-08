<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserGroup;

class GetGroupInfoService extends Service
{
	/**
	 * 根据部门获取组信息
	 * @param  [type] $department [description]
	 * @return [type]             [description]
	 */
	public function getGroupInfo($department)
	{
		if(!$department){
			$this->setError('部门ID不能为空');
			return false;
		}
		$result = UserGroup::find()
				->select(['id', 'name'])
				->where(['department_id' => $department])
				->asArray()
				->all();
		if(!$result){
			$this->setError('该部门无分组');
			return false;
		}
		$null = [
			'id' => '0',
			'name' => '无分组'
		];
		array_unshift($result, $null);
		return $result;
	}
}