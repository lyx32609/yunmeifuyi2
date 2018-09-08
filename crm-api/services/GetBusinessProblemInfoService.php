<?php
namespace app\services;

use app\foundation\Service;
use app\models\Problem;
use app\models\User;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\ProviceCity;
use app\models\Regions;
use app\models\CompanyCategroy;
use app\services\pushMessageService;


class GetBusinessProblemInfoService extends Service
{
	/**
	 * [提交问题]
	 * @param [type] $user_id                  用户id
	 * @param [type] $problem_title            问题标题
	 * @param [type] $problem_content          问题内容
	 * @param [type] $collaboration_department 协同部门
	 * @param [type] $priority                 优先级
	 */
	public function addProblem($user_id, $user_name, $problem_title, $problem_content,  $priority, $collaboration_department = null)
	{
		if(!$user_id) {

			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$user_name) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$problem_title) {
			$this->setError('问题标题不能为空');
			return false;
		}
		if(!$problem_content) {
			$this->setError('问题内容不能为空');
			return false;
		}
		if(!$priority) {
			$this->setError('优先级不能为空');
			return false;
		}

		if(!$collaboration_department) {
			$collaboration_department = 'null';
		}
		$company_data = 
		$user = User::find()
				->select(['name', 'domain_id', 'department_id',"company_categroy_id"])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$department = UserDepartment::find()
				->select(['name'])
				->where(['id' => $user['department_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();

		if(!$department['name']) {
			$department['name'] = '0';
		}
		$city_data = Regions::find()
				   ->select(["region_id","local_name","p_region_id"])
				   ->where(['region_id' => $user['domain_id']])
				   ->asArray()
				   ->one();
		$province_data = Regions::find()
					   ->select(["region_id","local_name"])
					   ->where(["region_id" => $city_data['p_region_id']])
					   ->asArray()
					   ->one();
		$result = new Problem;
		$result->user_id = $user_id;
		$result->user_name = $user['name'];
		$result->problem_title = $problem_title;
		$result->problem_content = $problem_content;
		$result->priority = $priority;
		$result->collaboration_department = $collaboration_department;
		$result->department = $department['name'];
		$result->area = $province_data['local_name'];
		$result->city = $city_data['local_name'];
		$result->create_time = time();
		$result->update_time = 0;
		$result->company_id = $user['company_categroy_id'];
		if(!$result->save()) {
			$this->setError('问题提交失败');
			return false;
		}
		return ['msg' => '问题提交成功'];
	}
	
	/**
	 * 获取业务问题列表
	 * @param  [type] $user_id 用户ID
	 */
	public function getQueryInfo($user_id, $problem_lock, $type)
	{
	
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		
		
		if ($problem_lock == 0) {
			$data = ['problem_id','create_time', 'problem_content', 'problem_title','priority', 'collaboration_department', 'user_name', 'area', 'city', 'department'];
		} else if ($problem_lock == 1) {
			$data = ['problem_id','update_time', 'problem_content','problem_title','priority', 'collaboration_department', 'user_name', 'area', 'city', 'department'];
		}
		$limit = 'priority desc';
		$user = User::find()
				->select(['domain_id', 'department_id', 'rank', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$company_categroy_id = CompanyCategroy::findOne(['id' => $user['company_categroy_id']]);
		$domain_id = $user['domain_id'];
		$user_city = Regions::find()
				  ->select(["region_id","local_name","p_region_id"])
				  ->where(["region_id" => $domain_id])
				  ->asArray()
				  ->one();
		$user_area = Regions::find()
					->select(["region_id","local_name"])
					->where(["region_id" => $user_city['p_region_id']])
					->asArray()
					->one();
        $user_department = UserDepartment::find()
				->select(['id','name'])
				->where(['id' => $user['department_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
		if($user['rank'] == 3) {
			if($type == 1){
				$result = Problem::find()
					->select($data)
					->where(['user_id' => $user_id])
					->andWhere(['problem_lock' => $problem_lock])
					->andWhere(['company_id' => $company_categroy_id->id])
					->orderBy($limit)
					->asArray()
					->all();
			}
			if($type == 2) {
				$result = Problem::find()
					->select($data)
					->where(['=', 'collaboration_department' , 'null'])
					->andWhere(['problem_lock' => $problem_lock])
					->andWhere(['city' => $user_city['local_name']])
					->andWhere(['company_id' => $company_categroy_id->id])
					->orderBy($limit)
					->asArray()
					->all();
			}
			if($type == 3) {
				$result = Problem::find()
					->select($data)
					->where(['!=', 'collaboration_department' , 'null'])
					->andWhere(['problem_lock' => $problem_lock])
					->andWhere(['city' => $user_city['local_name']])
					->andWhere(['company_id' => $company_categroy_id->id])
					->orderBy($limit)
					->asArray()
					->all();
			}
			
		}
		if($user['rank'] == 4){
			if($type == 1){
				$result = Problem::find()
						->select($data)
						->where(['user_id' => $user_id])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 2) {
				$department_user_id = User::find()
						->select(['id'])
						->where(['department_id' => $user_department['id']])
						->asArray()
						->all();
				for($i = 0; $i < count($department_user_id); $i++) {
					$department_user_id[$i] = $department_user_id[$i]['id'];
					// if($department_user_id[$i] == $user_department['id']) {
					// 	unset($department_user_id[$i]['id']);
					// }  之前未注释选择部门报错
				}
				$result = Problem::find()
						->select($data)
						->where(['in', 'user_id', $department_user_id])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 3) {
				$problem_all = Problem::find()
						->select($data)
						->where(['!=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
				$list = [];
				$j = 0;
				for($i = 0; $i < count($problem_all); $i++) {
					$list[$i]['collaboration_department'] = explode(',', $problem_all[$i]['collaboration_department']);
					if(in_array($user['department_id'], $list[$i]['collaboration_department'])) {
						$result[$j] = $problem_all[$i];
						$j++;
					}
				}
			}
		}
		if ($user['rank'] == 30) {
			if($type == 1) {
				$result = Problem::find()
						->select($data)
						->where(['user_id' => $user_id])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 2) {
				$result = Problem::find()
						->select($data)
						->where(['=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 3) {
				$result = Problem::find()
						->select($data)
						->where(['!=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			}
		}
		if($user['rank'] == 1 || $user['rank'] == 2) {
			if($type == 1){
				$result = Problem::find()
						->select($data)
						->where(['user_id' => $user_id])
						->andWhere(['problem_lock' => $problem_lock])
						->andWhere(['company_id' => $company_categroy_id->id])
						->orderBy($limit)
						->asArray()
						->all();
			} 
			if($type == 2){
				$this->setError('无部门数据');
				return false;
			}
			if($type == 3){
				$this->setError('无协同数据');
				return false;
			}
			
			
		}
		if(!$result) {
			$this->setError('暂无问题数据');
			return false;
		}
		for($i = 0; $i < count($result); $i++) {
			if($result[$i]['collaboration_department'] == 'null' ) {
				$result[$i]['collaboration_department'] = '';
			}
		}

		for($i = 0; $i < count($result); $i++) {
			if($result[$i]['department'] == '0' ) {
				$result[$i]['department'] = '';
			}
		}
		return  $result;
	}
	/**
	 * 管理员查看业务问题列表（接口暂时弃用）
	 * @param  [type] $user_id      [用户id]
	 * @param  [type] $problem_lock [0： 未解决  1：解决]
	 */
	public function getAdminProblem($user_id, $problem_lock)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;

		}
		if ($problem_lock == 0) {
			$data = ['problem_id','create_time', 'problem_content', 'problem_title','priority', 'collaboration_department', 'user_name', 'area', 'city', 'department'];
			
		} else if ($problem_lock == 1) {
			$data = ['problem_id','update_time', 'problem_content','problem_title','priority', 'collaboration_department', 'user_name', 'area', 'city', 'department'];
			
		}
		$limit = 'priority desc';
		$result = Problem::find()
			->select($data)
			->where(['user_id' => $user_id])
			->andWhere(['problem_lock' => $problem_lock])
			->orderBy($limit)
			->asArray()
			->all();
		for($i = 0; $i < count($result); $i++) {
				$result[$i]['ret'] = 1;
			}
		if(!$result) {
			$this->setError('暂无问题数据');
			return false;
		}
		return $result;
	}
	/**
	 * 修改协同部门
	 * @param  [type] $problem_id               [问题ID]
	 * @param  [type] $collaboration_department [协同部门]
	 */
	public function updateDepartment($problem_id, $collaboration_department)
	{
		if(!$problem_id) {
			$this->setError('问题ID不能为空');
			return false;
		}
		if(!$collaboration_department) {
			$collaboration_department = 'null';
		}

		$result = Problem::findOne($problem_id);
		if(!$result) {
			$this->setError('该问题数据不存在');
			return false;
		} 
		$result->collaboration_department = $collaboration_department;
		if(!$result->save()) {
			$this->setError('协同部门修改失败');
			return false;
		}
		return $result = '协同部门修改成功';
	}
	/**
	 * 问题解决
	 * @param  [type] $problem_id [问题ID]

	 */
	public function updateProblem($problem_id)
	{
		if(!$problem_id) {
			$this->setError('问题ID不能为空');
			return false;
		}
		$result = Problem::findOne($problem_id);
		if(!$result) {
			$this->setError('问题未找到');
			return false;
		}
		$result->problem_lock = 1;
		$result->update_time = time();
		if(!$result->save()) {
			$this->setError('操作失败');
			return false;
		}
		return $result = '操作成功';
	}
}