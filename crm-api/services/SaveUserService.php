<?php

namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\AuthAssignment;
use app\models\UserDepartment;

class SaveUserService extends Service
{
	public function saveUser($name, $rank, $item_name, $username, $phone, $group_id, $department, $password, $domain, $staffId)
	{
		if(!$username){
			$this->setError('用户名不能为空');
			return false;
		}
		$staff = User::findOne($staffId);
		if($staff->domain_id != $domain){
			$this->setError('不可跨区域添加员工');
			return false;
		}
		
		
		if(!$password){
			$this->setError('密码不能为空');
			return false;
		}
		if(!$group_id){
			$group_id = 0;
		}
		if(!$rank){
			$rank = 0;
		}
		if(!$phone){
			$phone = '0';
		}
		if(!$name){
			$name = '';
		}
		if(!$department){
			$department = 0;
		}
		$data = User::find()->where(['username' => $username])->asArray()->one();
		if($data){
			$this->setError('用户已存在');
			return false;
		}
		$user = new User;
		$user->name = $name;
		$user->rank = $rank;
		$user->username = $username;
		$user->staff_code = 0;
		$user->password = md5($password);
		$user->phone = $phone;
		$user->group_id = $group_id;
		$user->department_id = $department;
		$user->domain_id = $domain;
		$user->is_select = 1;
		if($user->save()){
			$user_id = $user->attributes['id'];
			if($item_name){
				$item = new AuthAssignment;
				$item->item_name = $item_name;
				$item->user_id = "" .$user_id ."";
				$item->created_at = time();
				if($item->save()){
					return $result = '添加成功';
				}
				$delete = User::find()->where(['id' => $user_id])->delete();
				$this->setError('请重新设置权限');
				return false;
			}
			return $result = '添加成功';
		}
		$this->setError('添加失败');
		return false;

	}
}