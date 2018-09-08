<?php

namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\AuthAssignment;

class UploadUserInfoNewService extends Service
{
	/**
	 * 修改员工信息接口
	 * @param  [type] $user_id   [员工id]
	 * @param  [type] $name      [员工名字]
	 * @param  [type] $rank      [员工等级]
	 * @param  [type] $item_name [员工权限]
	 * @param  [type] $username  [用户名]
	 * @param  [type] $phone     [手机号]
	 * @param  [type] $group_id  [组id]
	 * @return [type]            [description]
	 */
	public function uploadUserInfo($user_id, $name,$password ,$rank, $item_name,  $phone, $group_id, $department, $staffId)
	{
		if(!$user_id){
			$this->setError('用户id不能为空');
			return false;
		}
		if(!$staffId){
			$this->setError('登录人id不能为空');
			return false;
		}
		$staff = User::findOne($staffId);
		$user = User::findOne($user_id);
		if($staff->domain_id != $user->domain_id){
			$this->setError('不可跨区域修改员工信息');
			return false;
		}
		if(!$group_id){
			$group_id = 0;
		}
		if(!$rank){
			$rank = 0;
		}
		if(!$name){
			$name = '';
		}
		$result = User::find()
				->where(['id' => $user_id])
				->one();
		$result->name = $name;
		$result->rank = $rank;
		// if($phone){
		$result->phone = $phone;//手机号可以为空
		// }
		if($department){
			$result->department_id = $department;
		} else {
			$result->department_id = '0';
		}
		$result->group_id = $group_id;
		if(!$group_id){
			$result->group_id = 0;
		}
		if($password){
			$result->password = md5($password);
		}
		if(!$item_name){
			$result->is_staff = '0';
			$result->dimission_time = time();
		}else {
			$result->is_staff = '1';
			$result->dimission_time = 0;
		}
		if(!$result->save()){
		    var_dump($result->getErrors());exit;
			$this->setError('修改失败');
			return false;
		}
		if($item_name){
			$list = AuthAssignment::find()
					->where(['user_id' => $user_id])
					->one();
			if($list){
				$list->item_name = $item_name;
				$list->user_id = $user_id;
				$list->created_at = time();
				if(!$list->save()){
					$this->setError('权限修改失败');
					return false;
				}
				return $result = '修改成功';
			} else {
				$list = new AuthAssignment;
				$list->item_name = $item_name;
				$list->user_id = $user_id;
				$list->created_at = time();
				if(!$list->save()){
					$this->setError('权限修改失败');
					return false;
				}
				return $result = '修改成功';
			}
		} else {
			$list = AuthAssignment::find()
					->where(['user_id' => $user_id])
					->one();
			if(!$list){
				return $result = '修改成功';
			} else {
				if(!$list->delete()){
					$this->setError('权限修改失败');
					return false;
				}
				return $result = '修改成功';
			}
		}
	}
}