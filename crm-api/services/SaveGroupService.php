<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserGroup;
use app\models\UserDepartment;
use app\models\CompanyCategroy;

class SaveGroupService extends Service
{
	/**
	 * 添加组
	 * @param  [type] $department [部门id]
	 * @param  [type] $name       [组名]
	 * @param  [type] $domain     [地区]
	 * @return [type]             [description]
	 */
	public function saveGroup($department, $name, $domain)
	{
		if(!$department){
			$this->setError('部门id不能为空');
			return false;
		}
		$staff = UserDepartment::findOne($department);
	
		if($staff->domain_id != $domain){
			$this->setError('不可跨区域添加组');
			return false;
		}
		
		if(!$name){
			$this->setError('组名不能为空');
			return false;
		}
		if(!$domain){
			$this->setError('地区id不能为空');
			return false;
		}
		$group = UserGroup::find()
				->where(['domain_id' => $domain])
				->andWhere(['name' => $name])
				->andWhere(['department_id' => $department])
				->asArray()
				->one();
		if($group){
			$this->setError('分组已存在');
			return false;
		}
		$company = CompanyCategroy::findOne(['id' => $staff->company]);
		$result = new UserGroup;
		$result->name = $name;
		$result->desc = $name;
		$result->domain_id = $domain;
		$result->priority = 50;
		if($company->id == '1' || $company->fly == '1'){
		    $result->is_select = 1;
		} else {
		    $result->is_select = 0;
		}
		$result->department_id = $department;
		if(!$result->save()){
			$this->setError('组添加失败');
			return false;
		}
		return $result = '组添加成功';
	}
}