<?php
namespace app\services;

use app\foundation\Service;
use app\models\Problem;
use app\models\User;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\ProviceCity;
use app\models\ProblemStatus;

class GetQueryInfoNewService extends Service
{
	public function getQueryInfo($user_id, $problem_lock, $type)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if ($problem_lock == 0) {
			$data = ['problem_id','create_time', 'problem_content', 'problem_title','priority', 'collaboration_department', 'user_name', 'user_id', 'area', 'city', 'department'];
		} else if ($problem_lock == 1) {
			$data = ['problem_id','update_time', 'problem_content','problem_title','priority', 'collaboration_department', 'user_name', 'area', 'city', 'department'];
		}
		$limit = 'priority desc';
		$user = User::find()
				->select(['domain_id', 'department_id', 'rank', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		$user_city = UserDomain::find()
				->select(['region', 'are_region_id'])
				->where(['domain_id' => $user['domain_id']])
				->asArray()
				->one();
		$user_area = ProviceCity::find()
				->select(['province_name', 'province_id'])
				->where(['city_id' => $user_city['are_region_id']])
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
					->andwhere(['company_id' => $user['company_categroy_id']])
					->orderBy($limit)
					->asArray()
					->all();
			}
			if($type == 2) {
				$result = Problem::find()
					->select($data)
					->where(['=', 'collaboration_department' , 'null'])
					->andWhere(['problem_lock' => $problem_lock])
					->andWhere(['city' => $user_city['region']])
					->andwhere(['company_id' => $user['company_categroy_id']])
					->orderBy($limit)
					->asArray()
					->all();
			}
			if($type == 3) {
				$result = Problem::find()
					->select($data)
					->where(['!=', 'collaboration_department' , 'null'])
					->andWhere(['problem_lock' => $problem_lock])
					->andWhere(['city' => $user_city['region']])
					->andwhere(['company_id' => $user['company_categroy_id']])
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
						->andwhere(['company_id' => $user['company_categroy_id']])
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
					if($department_user_id[$i] == $user_department['id']) {
						unset($department_user_id[$i]['id']);
					}
				}
				$result = Problem::find()
						->select($data)
						->where(['in', 'user_id', $department_user_id])
						->andWhere(['problem_lock' => $problem_lock])
						->andwhere(['company_id' => $user['company_categroy_id']])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 3) {
				$problem_all = Problem::find()
						->select($data)
						->where(['!=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andwhere(['company_id' => $user['company_categroy_id']])
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
						->andwhere(['company_id' => $user['company_categroy_id']])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 2) {
				$result = Problem::find()
						->select($data)
						->where(['=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andwhere(['company_id' => $user['company_categroy_id']])
						->orderBy($limit)
						->asArray()
						->all();
			}
			if($type == 3) {
				$result = Problem::find()
						->select($data)
						->where(['!=', 'collaboration_department' , 'null'])
						->andWhere(['problem_lock' => $problem_lock])
						->andwhere(['company_id' => $user['company_categroy_id']])
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
						->andwhere(['company_id' => $user['company_categroy_id']])
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
			if($result[$i]['department'] == '0' ) {
				$result[$i]['department'] = '';
			}
			$list[$i]['status'] = $this->statusProblem($result[$i]['user_id'], $result[$i]['problem_id'], 1);
			if($list[$i]['status']){
				$result[$i]['status'] = '1';
			} else {
				$result[$i]['status'] = '2';
			}
		}
		return  $result;
	}
	/**
	 * 查看用户操作记录
	 * @param  [type] $user_id   [用户id]
	 * @param  [type] $status_id [类型id]
	 * @param  [type] $status    [类型]
	 * @return [type]            [description]
	 */
	public function statusProblem($user_id, $status_id, $status)
	{
		$result = ProblemStatus::find()
				->where(['user_id' => $user_id])
				->andWhere(['status_id' => $status_id])
				->andWhere(['status' => $status])
				->asArray()
				->one();
		if(!$result){
			return false;
		}
		return true;
	}
}