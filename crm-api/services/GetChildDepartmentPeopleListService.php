<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\User;
use app\models\AuthAssignment;
use app\models\UserGroup;

class GetChildDepartmentPeopleListService extends Service
{
	/**
	 * 获取子部门人员列表
	 * @param  [type] $department_id [description]
	 * @return [type]                [description]
	 */
	public function getChildDepartmentPeopleList($department_id, $type)
	{
		$department_name = UserDepartment::findOne($department_id);
		if(!$department_name){
			$this->setError('部门不存在');
			return false;
		}
		if($type == '0'){
			$data = UserDepartment::find()
					->select(UserDepartment::tableName().'.id,name')
					->with(['users' => function (\yii\db\ActiveQuery $query) {
	              			$query->select('department_id, id,username,password, name,phone,department_id, domain_id, group_id, rank');
	          			}])
					->where(UserDepartment::tableName() .'.parent_id = :parent_id',[':parent_id' => $department_id])
					->andWhere(['is_show' => 1])
					->asArray()
					->all();
			
			if(!$data){
				$list = $this->getUserList($department_id);
				if(!$list){
					return $result = [[
						'id' => $department_id,
						'name' => $department_name->name,
						'users' => []
					]];
				}
				for($i = 0; $i < count($list); $i++){
					$item[$i] = $this->checkLogin($list[$i]['id']);
					$list[$i]['item_name'] = $item[$i];
					if($list[$i]['group_id']){
						$list[$i]['groupname'] = $this->getGroup($list[$i]['group_id']);
					} else {
						$list[$i]['groupname'] = '';
					}
				}
				return $result = [[
					'id' => $department_id,
					'name' => $department_name->name,
					'users' => $list
				]];
			} else {
				for($i = 0; $i < count($data); $i++){
					for($j = 0; $j < count($data[$i]['users']); $j++){
						$item[$j] = $this->checkLogin($data[$i]['users'][$j]['id']);
						$data[$i]['users'][$j]['item_name'] = $item[$j];
						if($data[$i]['users'][$j]['group_id']){
							$data[$i]['users'][$j]['groupname'] = $this->getGroup($data[$i]['users'][$j]['group_id']);
						} else {
							$data[$i]['users'][$j]['groupname'] = '';
						}
					}
				}
				$user_list = $this->getUserList($department_id);
				if($user_list){
					for($i = 0; $i < count($user_list); $i++){
						$user_list[$i]['item_name'] = $this->checkLogin($user_list[$i]['id']);
						$user_list[$i]['groupname'] = $this->getGroup($user_list[$i]['group_id']);
					}
					array_unshift($data, [
							'id' => $department_id,
							'name' => $department_name->name,
							'users' => $user_list,
						]);
				} else {
					array_unshift($data, [
							'id' => $department_id,
							'name' => $department_name->name,
							'users' => []
						]);
				}
				
				
					
				
				return  $data;
			}
		}
		$result = User::find()
				->select(['id', 'username','password', 'name', 'phone', 'department_id', 'domain_id', 'group_id', 'rank'])
				->where(['department_id' => $department_id])
				->asArray()
				->all();
		if(!$result){
			$this->setError('暂无人员');
			return false;
		}
		for($i = 0; $i < count($result); $i++){
			$item[$i] = $this->checkLogin($result[$i]['id']);
			$result[$i]['item_name'] = $item[$i];
			if($result[$i]['group_id']){
				$result[$i]['groupname'] = $this->getGroup($result[$i]['group_id']);
			} else {
				$result[$i]['groupname'] = '';
			}
		}
		return $list = [
					'id' => $department_id,
					'name' => $department_name->name,
					'users' => $result
				];
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
				->select(['name'])
				->where(['id' => $group_id])
				->asArray()
				->one();
		if(!$data){
			return $data = '';
		}
		return $data['name'];
	}
	/**
	 * 获取用户集合
	 * @param  [type] $department_id [description]
	 * @return [type]                [description]
	 */
	public function getUserList($department_id)
	{
		$result = User::find()
				->select(['id', 'username','password', 'name', 'phone', 'department_id', 'domain_id', 'group_id', 'rank'])
				->where(['department_id' => $department_id])
				->asArray()
				->all();
		if($result){
			return $result;
		}
		return false;
	}
}