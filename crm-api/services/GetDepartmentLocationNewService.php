<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\UserGroup;
use app\models\UserLocation;
use app\benben\DateHelper;
use app\models\User;

class GetDepartmentLocationNewService extends Service
{
	/**
	 * 定位数据导出
	 * @param  [type] $department_id [部门id]
	 * @param  [type] $user_id       [用户id]
	 * @param  [type] $type          [1本日 2本周 3本月]
	 * @return [type]                [description]
	 */
	public function getDepartmentLocation($department_id, $user_id, $type)
	{
		if($type == '1'){
			$start = DateHelper::getTodayStartTime();
        	$end = DateHelper::getTodayEndTime();
		}
		if($type == '2'){
			$start = DateHelper::getWeekStartTime(0);
			$end = DateHelper::getThisWeekEndTime(0);
		}
		if($type == '3'){
			$start = DateHelper::getMonthStartTime(0);
			$end = DateHelper::getMonthEndTime(0);
		}
		if($department_id) {
			$department = UserDepartment::find()
					->select(['domain_id'])
					->where(['id' => $department_id])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			if(!$department){
				$this->setError('部门不存在');
				return false;
			}
			$group = UserGroup::find()
					->select(['id'])
					->where(['department_id' => $department_id])
					->asArray()
					->all();
			if(!$group){
				$user = User::find()
						->select(['username'])
						->where(['department_id' => $department_id])
						->andWhere(['domain_id' => $department['id']])
						->asArray()
						->all();
				
			} else {
				for($i = 0; $i < count($group); $i++){
					$group_id[$i] = $group[$i]['id'];
				}
				$user = User::find()
					->select(['username'])
					->where(['department_id' => $department_id])
					->andWhere(['domain_id' => $department['domain_id']])
					->andWhere(['in', 'group_id', $group_id])
					->asArray()
					->all();
			}
			if(!$user){
				$this->setError(['无用户数据']);
				return false;
			}
			for($i = 0; $i < count($user); $i++){
					$list[$i] = $user[$i]['username'];
			}
			$list = $this->getLocation($list, $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		if($user_id) {
			$user = User::find()
					->select(['username'])
					->where(['id' => $user_id])
					->asArray()
					->one();
			if(!$user){
				$this->setError('用户不存在');
				return false;
			}
			$list = $this->getLocation($user['username'], $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		return $list;
	}
	
	/**
	 * 查询员工业务定位记录
	 * @param  [type] $user  [用户名]
	 * @param  [type] $start [开始时间]
	 * @param  [type] $end   [结束时间]
	 * @return [type]        [description]
	 */
	public function getLocation($user, $start, $end)
	{
		if(is_array($user)){
			$list = UserLocation::find()
					->select(['shop_id', 'name', 'user', 'time', 'longitude', 'latitude', 'belong', 'reasonable', 'username'])
					->where(['in', 'user', $user])
					->andWhere(['between', 'time', $start, $end])
					->orderBy('user desc')
					->asArray()
					->all();
		}
		if(is_string($user)){
			$list = UserLocation::find()
					->select(['shop_id', 'name', 'user', 'time', 'longitude', 'latitude', 'belong', 'reasonable', 'username'])
					->where(['user' => $user])
					->andWhere(['between', 'time', $start, $end])
					->orderBy('time desc')
					->asArray()
					->all();
		}
		if(!$list){
			return false;
		}
		for($i = 0; $i < count($list); $i++){
			if(!$list[$i]['reasonable']){
				$list[$i]['reasonable'] = '';
			}
			if(!$list[$i]['username']){
				$list[$i]['username'] = '';
			}
        }
		return $list;
    }

	/**
	 * 查询员工信息
	 * @param  [type] $user [description]
	 * @return [type]       [description]
	 */
	public function selectUser($user)
	{
		$data = User::find()
				->select(['name'])
				->where(['username' => $user])
				->asArray()
				->one();
		return $data;
	}
}