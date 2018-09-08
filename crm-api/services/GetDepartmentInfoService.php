<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\User;
use app\models\UserDomain;


class GetDepartmentInfoService extends Service
{
	/**
	 * 获取用户所属地区全部协同部门
	 *  @param  [type] $user_id [用户id]
	 */
	public function getDepartmentInfo($user_id)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		$user = User::find()
				->select(['domain_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$list = UserDepartment::find()
				->select(['id','name'])
				->where(['domain_id' => $user['domain_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		for($i = 0; $i < count($list); $i++) {
			if($list[$i]['name'] == '离职部') {
				$list[$i] = [];
			}
		}
		$j = 0;
		for($i = 0; $i < count($list) + 1; $i++){
			if($list[$i]['id']){
				$result[$j] = $list[$i];
				$j++;
			}
		}
		if(!$result) {
			$this->setError('暂无协同部门数据');
			return false;
		}
		return $result;
	}
	/**
	 * 获取用户所属地区除本部门的所有协同部门
	 * @param  [type] $user_id [用户id]
	 */
	public function getDepartment($user_id)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		$user = User::find()
				->select(['domain_id','department_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$list = UserDepartment::find()
				->select(['id','name'])
				->where(['domain_id' => $user['domain_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		
		for($i = 0; $i < count($list); $i++) {
			if($list[$i]['id'] == $user['department_id'] || $list[$i]['name'] == '离职部'){
				$list[$i] = [];
			}
		}
		$j = 0;
		for($i = 0; $i < count($list) + 1; $i++){
			if($list[$i]['id']){
				$result[$j] = $list[$i];
				$j++;
			}
		}
		if(!$result) {
			$this->setError('暂无协同部门数据');
			return false;
		}
		return $result;
	}
	/**
	 * 获取用户所属地区除本部门的所有协同部门(改版后)
	 * @param  [type] $user_id [用户id]
	 */
	public function getDepartmentNew($user_id, $company_category_id)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		$user = User::find()
				->select(['domain_id','department_id', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$list = UserDepartment::find()
				->select(['id','name'])
				->where(['domain_id' => $user['domain_id']])
				->where(['company' => $user['company_categroy_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		
		for($i = 0; $i < count($list); $i++) {
			if($list[$i]['id'] == $user['department_id'] || $list[$i]['name'] == '离职部'){
				$list[$i] = [];
			}
		}
		$j = 0;
		for($i = 0; $i < count($list) + 1; $i++){
			if($list[$i]['id']){
				$result[$j] = $list[$i];
				$j++;
			}
		}
		if(!$result) {
			$this->setError('暂无协同部门数据');
			return false;
		}
		return $result;
	}
	public function getDepartmentInfoNew($user_id, $is_cooperation)
	{
	    if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		$user = User::find()
				->select(['domain_id','department_id', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$list = UserDepartment::find()
				->select(['id','name'])
				->where(['domain_id' => $user['domain_id']])
				->where(['company' => $user['company_categroy_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		if($is_cooperation == 0){
		    if(!$list){
		        $this->setError('暂无协同部门数据');
		        return false;
		    }
		    return $list;
		}
		for($i = 0; $i < count($list); $i++) {
			if($list[$i]['id'] == $user['department_id'] || $list[$i]['name'] == '离职部'){
				$list[$i] = [];
			}
		}
		$j = 0;
		for($i = 0; $i < count($list) + 1; $i++){
			if($list[$i]['id']){
				$result[$j] = $list[$i];
				$j++;
			}
		}
		if(!$result) {
			$this->setError('暂无协同部门数据');
			return false;
		}
		return $result;
	}
}
