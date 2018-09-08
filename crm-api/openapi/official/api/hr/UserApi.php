<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\UploadUserInfoNewService;
use app\models\User;
use app\models\CompanyCategroy;
class UserApi extends Api
{
	/**
	 * 循环更新用户公司id
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
			if(!$result[$i]['company_categroy_id']){
				$list[$i]['company_id'] = $this->getCategory($result[$i]['domain_id']);
				$list[$i]['id'] = $result[$i]['id'];
				if($list[$i]['company_id']){
				    $a[$i] = $this->update($list[$i]['id'], $list[$i]['company_id']);
				    if(!$a[$i]){
				        return ['msg' => $i];
				    }
				}
				
			}
		}
		
		return ['msg' => 'OK'];
	}
	public function getCategory($domain_id)
	{
		$result = CompanyCategroy::find()
				->select(['id'])
				->where(['domain_id' => $domain_id])
				->asArray()
				->one();
		if(!$result){
		    return false;
		}
		return $result['id'];
	}
	public function update($id, $company_categroy_id)
	{
		$result = User::find()
				->where(['id' => $id])
				->one();
		$result->company_categroy_id = $company_categroy_id;
		if(!$result->save()){
			return false;
		}
		return true;
	}
} 