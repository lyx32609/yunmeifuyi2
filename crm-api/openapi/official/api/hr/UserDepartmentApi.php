<?php

namespace official\api\hr;

use app\foundation\Api;
use app\models\UserDepartment;
use app\models\CompanyCategroy;

class UserDepartmentApi extends Api
{
	public function run()
	{
		/**
		 * 循环更新部门公司id
		 * @var [type]
		 */
		$result = UserDepartment::find()
				->select(['id', 'domain_id'])
				->asArray()
				->all();
		$list = [];
		for($i = 0; $i < count($result); $i++){
			$list[$i]['company_id'] = $this->getCategory($result[$i]['domain_id']);
			$list[$i]['id'] = $result[$i]['id'];
			if($list[$i]['company_id']){
			    $a[$i] = $this->update($list[$i]['id'], $list[$i]['company_id']);
			    if(!$a[$i]){
			        return ['msg' => $i];
			    }
			}
		}
		return ['msg' => 'ok'];
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
		$result = UserDepartment::find()
				->where(['id' => $id])
				->one();
		$result->company = $company_categroy_id;
		if(!$result->save()){
			return false;
		}
		return true;
	}
	
}