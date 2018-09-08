<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\UploadUserInfoNewService;
use app\models\User;
use app\models\CompanyCategroy;
use app\models\UserDomain;
class UserDomainApi extends Api
{
	/**
	 * 循环更新用户地区id
	 * @return [type] [description]
	 */
	public function run()
	{
		$result = User::find()
				->select(['id', 'domain_id', 'company_categroy_id'])
				->asArray()
				->all();
		$list = [];
		for($i =0; $i < count($result); $i++){
			$list[$i]['are_region_id'] = $this->getCategory($result[$i]['domain_id']);
			$list[$i]['id'] = $result[$i]['id'];
			if($list[$i]['are_region_id']){
				$a[$i] = $this->update($list[$i]['id'], $list[$i]['are_region_id']);
				if(!$a[$i]){
					return ['msg' => $i];
				}
			}
		}
		
		return ['msg' => 'OK'];
	}
	public function getCategory($domain_id)
	{
		$result = UserDomain::find()
				->select(['are_region_id'])
				->where(['domain_id' => $domain_id])
				->asArray()
				->one();
		if(!$result){
		    return false;
		}
		return $result['are_region_id'];
	}
	public function update($id, $are_region_id)
	{
		$result = User::find()
				->where(['id' => $id])
				->one();
		$result->domain_id = $are_region_id;
		if(!$result->save()){
			return false;
		}
		return true;
	}
} 