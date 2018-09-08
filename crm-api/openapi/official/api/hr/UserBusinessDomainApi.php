<?php

namespace official\api\hr;

use app\foundation\Api;
use app\models\UserBusiness;
use app\models\UserDomain;

class UserBusinessDomainApi extends Api
{
	public function run()
	{
		/**
		 * 循环更新Business表地区id
		 * @var [type]
		 */
	    $start = \Yii::$app->request->post ('start');
	    $end = \Yii::$app->request->post ('end');
		$result = UserBusiness::find()
				->select(['id', 'domain_id'])
				->where(['between', 'id', $start, $end])
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
		$result = UserBusiness::find()
				->where(['id' => $id])
				->one();
		$result->domain_id = $are_region_id;
		if(!$result->save()){
			return false;
		}
		return true;
	}
	
}