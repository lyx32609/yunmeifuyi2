<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\CompanyCategroy;
use app\models\CompanyBatch;
use app\models\CompanyReview;
use app\models\AuthAssignment;
class CompanyRegisterService extends Service
{
	/**
	 * 企业注册
	 * @param  [type] $username              [用户名]
	 * @param  [type] $password              [密码]
	 * @param  [type] $name                  [企业名称]
	 * @param  [type] $phone                 [手机号]
	 * @param  [type] $area_id               [省份id]
	 * @param  [type] $domain_id             [城市id]
	 * @param  [type] $status                [企业类型 0运营 1销售 2供货 3配送 4生产 5服务]
	 * @param  [type] $failure               [0永久使用  1试用]
	 * @param  [type] $license_num           [执照编号]
	 * @param  [type] $register_money        [注册资金]
	 * @param  [type] $business              [经营面积]
	 * @param  [type] $staff_num             [人员数量]
	 * @param  [type] $acting                [代理品牌]
	 * @param  [type] $proxy_level           [代理级别]
	 * @param  [type] $service_area          [供货、配送、运营为代理区域，生产为销售区域]
	 * @param  [type] $distribution_merchant [配送商户]
	 * @param  [type] $distribution_car      [配送车辆]
	 * @param  [type] $distribution_staff    [配送人员]
	 * @param  [type] $goods_num    		 [商品数量]
	 * @param  [type] $failure    			 [0永久使用  1试用]
	 * @param  [type] $goods_type		     [商品类型]
	 * @param  [type] $service_type		     [服务类型]
	 * @param  [type] $product_type		     [产品类型]
	 * @param  [type] $salas_business	     [服务区域]
	 * @return [type]                        [description]
	 */
	public function companyRegister($username, $password, $name, $phone, $area_id, $domain_id, $status, $failure, $license_num, $register_money, $business, $business_ress, $staff_num, $acting, $proxy_level, $service_area, $distribution_merchant, $distribution_car, $distribution_staff, $goods_num, $product, $goods, $service, $salas_business)
	{
		if(!$username){
			$this->setError('用户名不能为空');
			return false;
		}
		if(!$password){
			$this->setError('密码不能为空');
			return false;
		}
		if(!$name){
			$this->setError('企业名称不能为空');
			return false;
		}
		if(!$phone){
			$this->setError('手机号码不能为空');
			return false;
		}
		if(!$area_id){
			$this->setError('省份id不能为空');
			return false;
		}
		if(!$domain_id){
			$this->setError('区域id不能为空');
			return false;
		}
		if(!$status){
			$status = '0';
		}
		$review_lock = CompanyReview::findOne(1);
		if($failure == 1){
			$user_name = User::find()
					->where(['username' => $username])
					->asArray()
					->one();
			if($user_name){
				$this->setError('用户名已存在');
				return false;
			}
			$companyRegister = new CompanyCategroy();
			$companyRegister->name = $name;
			$companyRegister->status = $status;
			$companyRegister->createtime = time();
			$companyRegister->phone = $phone;
			$companyRegister->area_id = $area_id;
			$companyRegister->domain_id = $domain_id;
			$companyRegister->fly = 0;
			$companyRegister->type = 0;
			$companyRegister->review = $review_lock->review;
			$companyRegister->failure = $failure;
			$companyRegister->company_num = substr(md5(uniqid()),0,4);
			if(!$companyRegister->save()){
				$this->setError('企业添加失败');
				return false;
			}
			$company_id = $companyRegister->attributes['id'];
			$user = new User();
			$user->staff_code = 0;
			$user->username = $username;
			$user->password = md5($password);
			$user->phone = $phone;
			$user->name = $name;
			$user->domain_id = $domain_id;
			$user->group_id = 0;
			$user->department_id = 0;
			$user->is_select = 0;
			$user->rank = 30;
			$user->company_categroy_id = $company_id;
			$user->dimission_time = '0';
			$user->create_time = time();
			if(!$user->save()){
				$this->setError('用户添加失败');
				return false;
			}
			$user_id = $user->attributes['id'];
			$authManager = new AuthAssignment();
			$authManager->item_name = 'admin';
			$authManager->user_id = strval($user_id);
			$authManager->created_at = time();
			if(!$authManager->save()){
				$user_delete = User::findOne($user_id);
				$user_delete->delete();
				$company_delete = CompanyCategroy::findOne($company_id);
				$company_delete->delete();
				$this->setError('权限添加失败');
				return false;
			}
			return $result = [
			    'username' => $username,
			    'review' => $review_lock->review
			];
		}
		if($status == 2 || $status == 3){
			
			if(!$license_num){
				$this->setError('执照编号不能为空');
				return false;
			}
			if(!$register_money){
				$this->setError('注册资金不能为空');
				return false;
			}
			if(!$business){
				$this->setError('经营面积不能为空');
				return false;
			}
			if(!$business_ress){
				$this->setError('经营面地址不能为空');
				return false;
			}
			if(!$staff_num){
				$this->setError('人员数量不能为空');
				return false;
			}
			if(!$acting){
				$this->setError('代理品牌不能为空');
				return false;
			}
			if(!$proxy_level){
				$this->setError('代理级别不能为空');
				return false;
			}
			if(!$service_area){
				$this->setError('代理区域不能为空');
				return false;
			}
			if(!$distribution_merchant){
				$this->setError('配送商户不能为空');
				return false;
			}
			if(!$distribution_car){
				$this->setError('配送车辆不能为空');
				return false;
			}
			if(!$distribution_staff){
				$this->setError('配送人员不能为空');
				return false;
			}
			if(!$goods_num){
				$this->setError('商品数量不能为空');
				return false;
			}
			if(!$salas_business){
				$this->setError('服务区域不能为空');
				return false;
			}
		}
		if($status == 0){
			if(!$license_num){
				$this->setError('执照编号不能为空');
				return false;
			}
			if(!$register_money){
				$this->setError('注册资金不能为空');
				return false;
			}
			if(!$business){
				$this->setError('经营面积不能为空');
				return false;
			}
			if(!$business_ress){
				$this->setError('经营面地址不能为空');
				return false;
			}
			if(!$staff_num){
				$this->setError('人员数量不能为空');
				return false;
			}
			if(!$acting){
				$this->setError('代理品牌不能为空');
				return false;
			}
			if(!$proxy_level){
				$this->setError('代理级别不能为空');
				return false;
			}
			if(!$service_area){
				$this->setError('代理区域不能为空');
				return false;
			}
		}
		if($status == 4){
			if(!$license_num){
				$this->setError('执照编号不能为空');
				return false;
			}
			if(!$register_money){
				$this->setError('注册资金不能为空');
				return false;
			}
			if(!$business){
				$this->setError('经营面积不能为空');
				return false;
			}
			if(!$business_ress){
				$this->setError('经营面地址不能为空');
				return false;
			}
			if(!$staff_num){
				$this->setError('人员数量不能为空');
				return false;
			}
			if(!$acting){
				$this->setError('代理品牌不能为空');
				return false;
			}
			if(!$product){
				$this->setError('产品类型不能为空');
				return false;
			}
			if(!$service_area){
				$this->setError('销售区域不能为空');
				return false;
			}
			if(!$distribution_merchant){
				$this->setError('配送商户不能为空');
				return false;
			}
			if(!$distribution_car){
				$this->setError('配送车辆不能为空');
				return false;
			}
			if(!$distribution_staff){
				$this->setError('配送人员不能为空');
				return false;
			}
			if(!$goods_num){
				$this->setError('商品数量不能为空');
				return false;
			}
			if(!$salas_business){
				$this->setError('服务区域不能为空');
				return false;
			}
		}
		if($status == 5){
			if(!$license_num){
				$this->setError('执照编号不能为空');
				return false;
			}
			if(!$register_money){
				$this->setError('注册资金不能为空');
				return false;
			}
			if(!$business){
				$this->setError('经营面积不能为空');
				return false;
			}
			if(!$business_ress){
				$this->setError('经营面地址不能为空');
				return false;
			}
			if(!$staff_num){
				$this->setError('人员数量不能为空');
				return false;
			}
			if(!$service){
				$this->setError('服务类型不能为空');
				return false;
			}
		}
		if($status == 1){
			if(!$license_num){
				$this->setError('执照编号不能为空');
				return false;
			}
			if(!$register_money){
				$this->setError('注册资金不能为空');
				return false;
			}
			if(!$business){
				$this->setError('经营面积不能为空');
				return false;
			}
			if(!$business_ress){
				$this->setError('经营面地址不能为空');
				return false;
			}
			if(!$staff_num){
				$this->setError('人员数量不能为空');
				return false;
			}
			if(!$goods){
				$this->setError('商品分类不能为空');
				return false;
			}
		}
		if(strlen($username) < 6){
			$this->setError('用户名长度不能小于6位');
			return false;
		}
		$user_name = User::find()
				->where(['username' => $username])
				->asArray()
				->one();
		if($user_name){
			$this->setError('用户名已存在');
			return false;
		}
		$companyRegister = new CompanyCategroy();
		$companyRegister->name = $name;
		$companyRegister->status = $status;
		$companyRegister->createtime = time();
		$companyRegister->phone = $phone;
		$companyRegister->area_id = $area_id;
		$companyRegister->domain_id = $domain_id;
		$companyRegister->fly = 0;
		$companyRegister->type = 0;
		$companyRegister->review = $review_lock->review;
		$companyRegister->license_num = $license_num;
		$companyRegister->register_money = $register_money;
		$companyRegister->business = $business;
		$companyRegister->business_ress = $business_ress;
		$companyRegister->staff_num = $staff_num;
		$companyRegister->acting = $acting;
		$companyRegister->proxy_level = $proxy_level;
		$companyRegister->service_area = $service_area;
		$companyRegister->distribution_merchant = $distribution_merchant;
		$companyRegister->distribution_car = $distribution_car;
		$companyRegister->distribution_staff = $distribution_staff;
		$companyRegister->goods_num = $goods_num;
		$companyRegister->failure = $failure;
		$companyRegister->goods_type = $goods_type;
		$companyRegister->service_type = $service_type;
		$companyRegister->product_type = $product_type;
		$companyRegister->salas_business = $salas_business;
		$companyRegister->company_num = substr(md5(uniqid()),0,4);
		//查询 公司编码，若重复重新生成
        // $re = CompanyCategroy::find()->select('company_num')->asArray()->all();
        // foreach ($re as $key=>$value){
        //     $res[] = $value['company_num'];
        // }
        // while (in_array($companyRegister->company_num,$res)){
        //     $companyRegister->company_num =  substr(md5(uniqid()),0,4);
        // }
		if(!$companyRegister->save()){
			$this->setError('企业添加失败');
			return false;
		} 
		$company_id = $companyRegister->attributes['id'];
		$user = new User();
		$user->staff_code = 0;
		$user->username = $username;
		$user->password = md5($password);
		$user->phone = $phone;
		$user->name = $name;
		$user->domain_id = $domain_id;
		$user->group_id = 0;
		$user->department_id = 0;
		$user->is_select = 0;
		$user->rank = 30;
		$user->company_categroy_id = $company_id;
		$user->dimission_time = '0';
		if(!$user->save()){
			$this->setError('用户添加失败');
			return false;
		}
		$user_id = $user->attributes['id'];
		$authManager = new AuthAssignment();
		$authManager->item_name = 'admin';
		$authManager->user_id = strval($user_id);
		$authManager->created_at = time();
		if(!$authManager->save()){
			$user_delete = User::findOne($user_id);
			$user_delete->delete();
			$company_delete = CompanyCategroy::findOne($company_id);
			$company_delete->delete();
			$this->setError('权限添加失败');
			return false;
		}
		if($distribution_car){
			
			$batch = json_decode($distribution_car, true);
			foreach ($batch as $key => $value) {
				if(!$value['type'] || !$value['num']){
					unset($batch[$key]);
				}
			}
			for($i = 0; $i < count($batch); $i++){
				$company_batch = new CompanyBatch();
				$company_batch->type = $batch[$i]['type'];
				$company_batch->num = $batch[$i]['num'];
				$company_batch->company_id = $company_id;
				if(!$company_batch->save()){
					$user_delete = User::findOne($user_id);
					$user_delete->delete();
					$company_delete = CompanyCategroy::findOne($company_id);
					$company_delete->delete();
					$batch_delete =  CompanyBatch::deleteAll(['company_id' => $company_id]);
					$this->setError('车辆添加失败');
					return false;
				}
			}
		}
		 return $result = [
			    'username' => $username,
			    'review' => $review_lock->review
			];
	}

	/*展示企业信息*/
	public function showCompanyInfo($companyId)
	{
		if(!$companyId)
		{
			$this->setError('企业ID不能为空');
			return false;
		}
		$companyInfo = CompanyCategroy::find()
					->select(["off_user.username","off_user.phone","off_company_categroy.*","off_company_goods.goods_name","off_company_service.service_name","off_company_product.product_name"])
					->where(["off_company_categroy.id"=>$companyId])
					->leftJoin('off_company_goods', 'off_company_categroy.goods_type = off_company_goods.id')
					->leftJoin('off_company_service','off_company_categroy.service_type = off_company_service.id')
					->leftJoin('off_company_product','off_company_categroy.product_type = off_company_product.id')
					->leftJoin('off_user','off_company_categroy.id = off_user.company_categroy_id')
					->asArray()
					->one();
		// $userInfo = User::find()
		// 		//->select()
		// 		->where(["company_categroy_id"=>$companyId])
		// 		->asArray()
		// 		->one();
		return $companyInfo;

	}
}
