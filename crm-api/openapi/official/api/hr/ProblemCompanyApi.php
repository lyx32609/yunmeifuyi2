<?php

namespace official\api\hr;

use app\foundation\Api;
use app\models\Problem;
use app\models\User;

class ProblemCompanyApi extends Api
{
	public function run()
	{
		/**
		 * 循环更新问题公司id
		 * @var [type]
		 */
		$result = Problem::find()
				->select(['problem_id', 'user_id', 'company_id'])
				->asArray()
				->all();
		$list = [];
		for($i = 0; $i < count($result); $i++){
		    if(!$result[$i]['company_id']){
		        $list[$i]['company_id'] = $this->getCategory($result[$i]['user_id']);
		        $list[$i]['id'] = $result[$i]['problem_id'];
		        if($list[$i]['company_id']){
		            $a[$i] = $this->update($list[$i]['id'], $list[$i]['company_id']);
    		        if(!$a[$i]){
    		            return ['msg' => $i];
    		        }
		        }
		        
		    }
			
		}
		return ['msg' => 'ok'];
	}
	public function getCategory($domain_id)
	{
		$result = User::find()
				->select(['company_categroy_id'])
				->where(['id' => $domain_id])
				->asArray()
				->one();
		if(!$result){
		    return false;
		}
		return $result['company_categroy_id'];
	}
	public function update($id, $company_categroy_id)
	{
		$result = Problem::find()
				->where(['problem_id' => $id])
				->one();
		$result->company_id = $company_categroy_id;
		if(!$result->save()){
		    var_dump($id);exit;
		    var_dump($result->getErrors());exit;
			return false;
		}
		return true;
	}
	
}