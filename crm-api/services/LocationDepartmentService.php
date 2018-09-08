<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserLocation;
use app\models\UserDomain;

class LocationDepartmentService extends Service
{
	public function locationDepartment($start, $end)
	{
		$result = UserLocation::find()
				->select(['id', 'domain'])
				->where(['between', 'id', $start, $end])
				->asArray()
				->all();
		$list = [];
		for($i =0; $i < count($result); $i++){
			if($result[$i]['domain'] < 40){
				$list[$i]['are_region_id'] = $this->getCategory($result[$i]['domain']);
				$list[$i]['id'] = $result[$i]['id'];
				if($list[$i]['are_region_id']){
				    $a[$i] = $this->update($list[$i]['id'], $list[$i]['are_region_id']);
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
		$result = UserLocation::find()
				->where(['id' => $id])
				->one();
		$result->domain = $are_region_id;
		if(!$result->save()){
			var_dump($id, $are_region_id);
			var_dump($result->getErrors());exit;
			return false;
		}
		return true;
	}
}