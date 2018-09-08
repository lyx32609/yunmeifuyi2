<?php

namespace official\api\hr;

use app\foundation\Api;
use app\models\User;
use app\models\AuthAssignment;
class UserLeverApi extends Api
{
	/**
	 * 循环更新离职人员变更
	 * @return [type] [description]
	 */
	public function run()
	{
		$result = User::find()
				->select(['id'])
				->asArray()
				->all();
		$list = [];
		for($i =0; $i < count($result); $i++){
			$list[$i]['id'] = $this->getCategory($result[$i]['id']);
			if(!$list[$i]['id']){
				$a[$i] = $this->update($result[$i]['id']);
				if(!$a[$i]){
					return $i;
				}
			}
		}
		
		return ['msg' => 'OK'];
	}
	public function getCategory($domain_id)
	{
		$result = AuthAssignment::find()
				->select(['user_id'])
				->where(['user_id' => $domain_id])
				->asArray()
				->one();
		if(!$result){
		    return false;
		}
		return $result['user_id'];
	}
	public function update($id)
	{
		$result = User::find()
				->where(['id' => $id])
				->one();
		$result->is_staff = '0';
		$result->dimission_time = time();
		if(!$result->save()){
			return false;
		}
		return true;
	}
} 