<?php
namespace app\services;

use app\foundation\Service;
use app\models\Regions;
use app\models\UserDomain;
use app\models\CompanyCategroy;
use app\models\UserDepartment;
use app\models\User;

class GetAreaCityPanyListService extends Service
{
	/**
	 * 获取省市公司接口
	 * @return [type] [description]
	 */
	public function getAreaCityPanyList($user_id, $company_category_id, $rank, $fly, $area, $city, $company, $type, $status = null)
	{
		if(!$user_id){
			$this->setError('用户id不能为空');
			return false;
		}
		if(!$company_category_id){
			$this->setError('公司id不能为空');
			return false;
		}
		if(!$rank){
			$this->setError('用户级别不能为空');
			return false;
		}
		$result = [];
		if($type){
		   $company_list = $this->getCompanyList($company_category_id, $area, $city, $company);
		   if(!$company_list){
		       $this->setError('暂无数据');
		       return false;
		   }
		   // $result = $this->getDepartmentList($company_list); 之前是直接查询的所有部门，之后修改为rank 4 查询自己部门
	         if (($rank == 4 && $fly == 0) || ($rank == 4 && $fly == 1)){
                $result = $this->getDepartmentListTwo($user_id);
             }else {
                $result = $this->getDepartmentList($company_list);
             }
		   if(!$result){
		       $this->setError('暂无数据');
		       return false;
		   }
		   return $result;
		}
		
		if($rank == 30 && $fly == 0){
			$result = Regions::find()
				->select(Regions::tableName().'.region_id,local_name')
					->with(['regions' => function (\yii\db\ActiveQuery $query) { 
	              			$query->select('p_region_id,region_id,local_name,p_region_id')->where('region_grade = :region_grade',[':region_grade' => 2]);
	          			}])
					->where(Regions::tableName() .'.region_grade = :region_grade',[':region_grade' => 1])
					->asArray()
					->all();
	        if($status == '1') {
	            for($i = 0; $i < count($result); $i++){
	                for($j = 0; $j < count($result[$i]['regions']); $j++){
	            
	                    $result[$i]['regions'][$j]['company'] = $this->getCompany($company_category_id, $result[$i]['regions'][$j]['region_id'], $fly);
	                }
	            }
	            return $result;
	        }
			$company = [];
			$all_city = [
			    
			        'p_region_id' => '-2',
			        'region_id' => '-2',
					'local_name' => '全部',
			    ];
			$all_company = [
			    
			         'id' => '-3',
			        'name' => '全部'
			    ];
			for($i = 0; $i < count($result); $i++){
			    array_unshift($result[$i]['regions'], $all_city);
				for($j = 0; $j < count($result[$i]['regions']); $j++){
				    
					$result[$i]['regions'][$j]['company'] = $this->getCompany($company_category_id, $result[$i]['regions'][$j]['region_id'], $fly);
					array_unshift($result[$i]['regions'][$j]['company'], $all_company);
				}
			}

			$child_id = CompanyCategroy::find()
					->select(['id'])
					->where(['fly' => $company_category_id])
					->asArray()
					->all();
			$company_id = [$company_category_id];		
			if($child_id){
				array_unshift($company_id, $child_id);
			} 
			$department = UserDepartment::find()
					->select(['id', 'name'])
					->where(['in', 'company', $company_id])
					->andWhere(['is_show' => 1])
					->GroupBy('name')
					->asArray()
					->all();
			$data = [
				'region_id' => '-1',
				'local_name' => '全国',
				'regions' => [[
					'p_region_id' => '-2',
					'region_id' => '-2',
					'local_name' => '全部',
					'company' =>[[
						'id' => '-3',
						'name' => '全部']
					]]
				]
			];
			array_unshift($result, $data);
		} else if(($rank == 4 && $fly == 0) || ($rank == 4 && $fly == 1)){
			$company_domain = CompanyCategroy::findOne($company_category_id);
			$company = $this->getFlyCompany($company_category_id, $company_domain->domain_id);
			
			$result = $this->getArea($company_domain->domain_id);
			
			$result['regions'] = [$this->getCity($company_domain->domain_id)]; 
			$result['regions'][0]['company'] = [$company];
			return [$result];
			// $result['regions']['company']['department'] = $this->getDepartment($company_category_id, $company_domain->domain_id, $user_id);
		} else if($rank == 3 && $fly == 1){
			$company_domain = CompanyCategroy::findOne($company_category_id);
			$company = $this->getFlyCompany($company_category_id, $company_domain->domain_id);
			$result = $this->getArea($company_domain->domain_id);
			$result['regions'] = [$this->getCity($company_domain->domain_id)];
			$result['regions'][0]['company'] = [$company];
			return [$result];
			// $result['regions']['company']['department'] = $this->getDepartment($company_category_id, $company_domain->domain_id);
		} else {
			$this->setError('暂无权限');
			return false;
		}
		return $result;
	}
	
	/**
	 * 获取区域内所有公司
	 * @param  [type] $city [description]
	 * @return [type]       [description]
	 */
	public function getCompany($company_category_id, $company_domain, $fly)
	{
		if($fly == 0){
			$fly_company = $this->getFlyCompany($company_category_id, $company_domain);
			if(!$fly_company){
				$result = $this->getChildCompany($company_category_id, $company_domain);
			} else {
				$result = $this->getChildCompany($company_category_id, $company_domain);
				if($result){
					array_unshift($result, $fly_company);
				} else {
				    return [$fly_company];
				}
			}
		} else {
			$result = $this->getChildCompany($company_category_id, $company_domain);
		}
		if(!$result){
			$result = [];
		}
		return $result;
	}
	/**
	 * 获取主公司
	 * @param  [type] $company_category_id [description]
	 * @param  [type] $company_domain      [description]
	 * @return [type]                      [description]
	 */
	public function getFlyCompany($company_category_id, $company_domain)
	{
		$result = CompanyCategroy::find()
				->select(['id', 'name'])
				->where(['id' => $company_category_id])
				->andWhere(['domain_id' => $company_domain])
				->asArray()
				->one();
		if(!$result){
			return [];
		}
		return $result;
	}
	/**
	 * 获取子公司
	 * @param  [type] $company_category_id [description]
	 * @param  [type] $company_domain      [description]
	 * @return [type]                      [description]
	 */
	public function getChildCompany($company_category_id, $company_domain)
	{
		$result = CompanyCategroy::find()
					->select(['id', 'name'])
					->where(['domain_id' => $company_domain])
					->andWhere(['fly' => $company_category_id])
					->asArray()
					->all();
		if(!$result){
			return [];
		}
		return $result;
	}
	/**
	 * 获取部门
	 * @param  [type] $company_category_id [description]
	 * @param  [type] $company_domain      [description]
	 * @return [type]                      [description]
	 */
	public function getDepartment($company_category_id, $company_domain, $user_id = null)
	{
		if(!$user_id) {
			$result = UserDepartment::find()
				->select(['id', 'name'])
				->where(['domain_id' => $company_domain])
				->andWhere(['company' => $company_category_id])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
			if(!$result){
				return [];
			}
			return $result;
		}
		$user = User::findOne($user_id);
		$result = UserDepartment::find()
				->select(['id', 'name'])
				->where(['id' => $user->department_id])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
		if(!$result) {
			return [];
		}
		return $result;
	}
	/**
	 * 获取省份名称
	 * @param  [type] $area [description]
	 * @return [type]       [description]
	 */
	public function getArea($city_id)
	{
		$regions = Regions::find()
				->select(['p_region_id'])
				->where(['region_id' => $city_id])
				->andWhere(['region_grade' => 2])
				->asArray()
				->one();
		$area = Regions::find()
				->select(['region_id', 'local_name'])
				->where(['region_id' => $regions['p_region_id']])
				->andWhere(['region_grade' => 1])
				->asArray()
				->one();
		return $area;
	}
	/**
	 * 获取城市名称
	 * @param  [type] $city_id [description]
	 * @return [type]          [description]
	 */
	public function getCity($city_id)
	{
		$regions = Regions::find()
				->select(['region_id', 'local_name'])
				->where(['region_id' => $city_id])
				->andWhere(['region_grade' => 2])
				->asArray()
				->one();
		return $regions;
	}
	/**    
	 * 获取公司列表集合
	 * @param unknown $user_company
	 * @param unknown $area
	 * @param unknown $city
	 * @param unknown $company
	 * @return unknown[]|boolean
	 */
	public function getCompanyList($user_company, $area, $city, $company)
	{
	    
	    if($area == '-1'){
	       $company_categroy_fly = CompanyCategroy::findOne(['id' => $user_company]);
	       $company_list = CompanyCategroy::find()
		            ->select(['id'])
		            ->where(['fly' => $user_company])
		            ->asArray()
		            ->all();
	        if(!$company_list){
	            return [$user_company];
	        }
	        $result = [];
	        for($i = 0; $i < count($company_list); $i++){
	            $result[$i] = $company_list[$i]['id'];
	        }
	        array_unshift($result, $user_company);
	        return $result;
	    } else if($area != '-1'){
	        $company_categroy_fly = CompanyCategroy::findOne(['id' => $user_company]);
	        if($company_categroy_fly){
	            if($city == '-2'){
	                if($company_categroy_fly->area_id == $area){
	                    $company_list = CompanyCategroy::find()
        	                    ->select(['id'])
        	                    ->where(['fly' => $user_company])
        	                    ->andWhere(['area_id' => $area])
        	                    ->asArray()
        	                    ->all();
	                    if(!$company_list){
	                        return [$user_company];
	                    }
	                    $result = [];
	                    for($i = 0; $i < count($company_list); $i++){
	                        $result[$i] = $company_list[$i]['id'];
	                    }
	                    array_unshift($result, $user_company);
	                    return $result;
	                } else {
	                    $company_list = CompanyCategroy::find()
        	                    ->select(['id'])
        	                    ->where(['fly' => $user_company])
        	                    ->andWhere(['area_id' => $area])
        	                    ->asArray()
        	                    ->all();
	                    if(!$company_list){
	                        return [$user_company];
	                    }
	                    $result = [];
	                    for($i = 0; $i < count($company_list); $i++){
	                        $result[$i] = $company_list[$i]['id'];
	                    }
	                    return $result;
	                }
	            } else if($city != '-2'){
	                 $company_categroy_fly = CompanyCategroy::findOne(['id' => $user_company]);
	                 
                     if($company_categroy_fly){
                         if($company == '-3'){ 
                             if($company_categroy_fly->domain_id == $city){
                                $company_list = CompanyCategroy::find()
                                     ->select(['id'])
                                     ->where(['fly' => $user_company])
                                     ->andWhere(['area_id' => $area])
                                     ->andWhere(['domain_id' => $city])
                                     ->asArray()
                                     ->all();
                                 if(!$company_list){
                                     return [$user_company];
                                 }
                                 $result = [];
                                 for($i = 0; $i < count($company_list); $i++){
                                     $result[$i] = $company_list[$i]['id'];
                                 }
                                 array_unshift($result, $user_company);
                                 return $result;
                             } else {
                                 $company_list = CompanyCategroy::find()
                                         ->select(['id'])
                                         ->where(['fly' => $user_company])
                                         ->andWhere(['area_id' => $area])
                                         ->andWhere(['domain_id' => $city])
                                         ->asArray()
                                         ->all();
                                 if(!$company_list){
                                     return [$user_company];
                                 }
                                 $result = [];
                                 for($i = 0; $i < count($company_list); $i++){
                                     $result[$i] = $company_list[$i]['id'];
                                 }
                                 return $result;
                             }
                     } else {
                         return [$company];
                     }
	            } else {
                     return false;
                 }
	        } else{
	            return  false;
	        }
	        
	    } else {
	        return false;
	    }
	    }
	 }
	 /**
	  * 获取部门集合
	  * @param unknown $company_list
	  */
	 public function getDepartmentList($company_list)
	 {
	     $result = UserDepartment::find()      
	               ->select(['id', 'name'])
	               ->where(['in', 'company', $company_list])
	               ->andWhere(['is_show' => 1])
	               ->andWhere(['<>','id','4'])      //去除离职部
	               ->groupBy(['name'])
	               ->asArray()
	               ->all();
	     if(!$result){
	         return false;
	     }
	     return $result;
	             
	 }
	 /**
	  * 获取省市公司接口（去掉全部）
	  * @return [type] [description]
	  */
	 public function getAreaCityPanyListNew($user_id, $company_category_id, $rank, $fly, $area, $city, $company, $type)
	 {
	     if(!$user_id){
	         $this->setError('用户id不能为空');
	         return false;
	     }
	     if(!$company_category_id){
	         $this->setError('公司id不能为空');
	         return false;
	     }
	     if(!$rank){
	         $this->setError('用户级别不能为空');
	         return false;
	     }
	     $result = [];
	     if($type){
	         $company_list = $this->getCompanyList($company_category_id, $area, $city, $company);
	         if(!$company_list){
	             $this->setError('暂无数据');
	             return false;
	         }
	         // $result = $this->getDepartmentList($company_list); 之前是直接查询的所有部门，之后修改为rank 4 查询自己部门
	         if (($rank == 4 && $fly == 0) || ($rank == 4 && $fly == 1)){
                $result = $this->getDepartmentListTwo($user_id);
             }else {
                $result = $this->getDepartmentList($company_list);
             }
	         if(!$result){
	             $this->setError('暂无数据');
	             return false;
	         }
	         return $result;
	     }
	 
	     if($rank == 30 && $fly == 0){
	         $result = Regions::find()
        	         ->select(Regions::tableName().'.region_id,local_name')
        	         ->with(['regions' => function (\yii\db\ActiveQuery $query) {
        	             $query->select('p_region_id,region_id,local_name,p_region_id')->where('region_grade = :region_grade',[':region_grade' => 2]);
        	         }])
        	         ->where(Regions::tableName() .'.region_grade = :region_grade',[':region_grade' => 1])
        	         ->asArray()
        	         ->all();
	         for($i = 0; $i < count($result); $i++){
	             for($j = 0; $j < count($result[$i]['regions']); $j++){
	                   $result[$i]['regions'][$j]['company'] = $this->getCompany($company_category_id, $result[$i]['regions'][$j]['region_id'], $fly);
	             }
	         }
	 
	     } else if(($rank == 4 && $fly == 0) || ($rank == 4 && $fly == 1)){
	         $company_domain = CompanyCategroy::findOne($company_category_id);
	         $company = $this->getFlyCompany($company_category_id, $company_domain->domain_id);
	         $result = $this->getArea($company_domain->domain_id);
	         $result['regions'] = [$this->getCity($company_domain->domain_id)];
	         $result['regions'][0]['company'] = [$company];
	         return [$result];
	     } else if($rank == 3 && $fly == 1){
	         $company_domain = CompanyCategroy::findOne($company_category_id);
	         $company = $this->getFlyCompany($company_category_id, $company_domain->domain_id);
	         $result = $this->getArea($company_domain->domain_id);
	         $result['regions'] = [$this->getCity($company_domain->domain_id)];
	         $result['regions'][0]['company'] = [$company];
	         return [$result];
	     } else {
	         $this->setError('暂无权限');
	         return false;
	     }
	     return $result;
	 }
	 /**
     * @param $user_id
     * @return array|\yii\db\ActiveRecord[]
     * 获取rank 为4 的部门名称
     */
	 public function getDepartmentListTwo($user_id)
     {
         $department = User::find()->select('department_id')->where(['id'=>$user_id])->asArray()->one();
         $res = UserDepartment::find()
             ->select(['id','name'])
             ->where(['id'=>$department['department_id']])
             ->andWhere(['is_show' => 1])
             ->asArray()
             ->all();
         return $res;
     }

}