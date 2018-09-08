<?php
namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\User;
use app\models\UserDepartment;
use app\models\GroupDepartment;
use app\models\UserDomain;
use app\models\ProviceCity;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
class GetBusinessDataService extends Service
{
	private $api = 'statistics/statisticsDataTimer'; //计划任务接口
	private $department_api = 'businessrecord/userRecordNew'; //调取员工业务记录接口
	private $user_api = 'statistics/userRealTimeStatisticsData'; //获取个人当天实时数据接口
	/**
	 * 业务数据统计接口
	 * @param  [type] $area       [省份]
	 * @param  [type] $city       [城市]
	 * @param  [type] $department [部门]
	 * @return [type]             [description]
	 */
	public function getBusinessData($area, $city, $department)
	{
		if(!$area) {
			$this->setError('省份不能为空');
			return false;
		}
		if(!$department){
			$this->setError('部门不能为空');
			return false;
		}
		if($area == '全国'){
			$department_id = UserDepartment::find()
				->select(['id','is_select'])
				->where(['name' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
			$result = [];
			if(count($department_id) > 1){
				for($i = 0; $i < count($department_id); $i++){
					if(!$department_id[$i]['is_select']){
						unset($department_id[$i]['is_select']);
					}
					for($j = 1; $j < 4; $j ++) {
						$list[$i][$j] = $this->userRecord(30, $department_id[$i]['id'], 0, $j);
					}
				}
				//var_dump($list);exit;
				for($i = 0; $i < count($department_id); $i++){
					if(!$department_id[$i]['is_select']){
						unset($department_id[$i]['is_select']);
					}
					$user_department[$i] = $this->departmentRecord($department_id[$i]['id']);
				}
				
				for($i = 0; $i < count($department_id); $i++) {
					$user_department[$i] = $department_id[$i]['id'];
				}
				$department_user = $this->departmentUser($user_department);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];
				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
				for($i = 0; $i < count($list); $i++) {
					for($j  = 1; $j < 4; $j++) {
						$list[$j]['visit'] += $list[$i][$j]['result']['visit']; 
						$list[$j]['visit_contrast'] += $list[$i][$j]['result']['visit_contrast']; 
						$list[$j]['register'] += $list[$i][$j]['result']['register']; 
						$list[$j]['register_contrast'] += $list[$i][$j]['result']['register_contrast']; 
						$list[$j]['order_num_contrast'] += $list[$i][$j]['result']['order_num_contrast']; 
						$list[$j]['order_amount'] += $list[$i][$j]['result']['order_amount']; 
						$list[$j]['order_amount_contrast'] += $list[$i][$j]['result']['order_amount_contrast']; 
						$list[$j]['order_num'] += $list[$i][$j]['result']['order_num']; 
						$list[$j]['customer_num'] += $list[$i][$j]['result']['customer_num']; 
						$list[$j]['customer_num_contrast'] += $list[$i][$j]['result']['customer_num_contrast']; 
					}
				}
			}else {
				if(!$department_id[0]['is_select']){
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord('30', $department_id[0]['id'], '0', $j);
					$list[$j] = $list[$j]['result'];
				}
				for($i = 0; $i < count($department_id); $i++) {
					$user_department[$i] = $department_id[$i]['id'];
				}
				$department_user = $this->departmentUser($user_department);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];

				}
				$department_day = $this->departmentDay($user_list[0]);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
			}
		}
		if($area != '全国') {
			if($city == '全部'){
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['province_name' => $area])
						->asArray()
						->all();
				for($i = 0; $i < count($domain_id); $i++) {
					$domain_id[$i] = $domain_id[$i]['city_id'];
				}
				$department_id = UserDepartment::find()
						->select(['id','is_select'])
						->where(['in','domain_id', $domain_id])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				if(!$department_id['is_select']) {
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord('30', $department_id['id'], '0', $j);
					$list[$j] = $list[$j]['result'];
				}
				$department_user = $this->departmentUser($department_id['id']);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];
				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}

			}
			if($city != '全部') {
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['city_name' => $city])
						->asArray()
						->one();
				$department_id = UserDepartment::find()
						->select(['id', 'is_select'])
						->where(['domain_id' => $domain_id['city_id']])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				if(!$department_id) {
					$this->setError('该城市暂无相应部门');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord('30', $department_id['id'], '0', $j);
					$list[$j] = $list[$j]['result'];
				}
				$department_user = $this->departmentUser($department_id['id']);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];

				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
			}
		}
		$visit_clent = [
			'day' => $department_day['result']['visit'] ? $department_day['result']['visit'] : 0,
			'week' => $list[2]['visit'] ? $list[2]['visit'] : 0,
			'month' => $list[3]['visit'] ? $list[3]['visit'] : 0,
			'yesterday' => $list[1]['visit_contrast'] ? $list[1]['visit_contrast'] : 0,
			'last_week' => $list[2]['visit_contrast'] ? $list[2]['visit_contrast'] : 0,
			'last_month' => $list[3]['visit_contrast'] ? $list[3]['visit_contrast'] : 0,
		];
		
		$register = [
			'day' => $department_day['result']['registerNum']['day'] ? $department_day['result']['registerNum']['day'] : 0,
			'week' => $list[2]['register'] ? $list[2]['register'] : 0,
			'month' => $list[3]['register'] ? $list[3]['register'] : 0,
			'yesterday' => $list[1]['register_contrast'] ? $list[1]['register_contrast'] : 0,
			'last_week' => $list[2]['register_contrast'] ? $list[2]['register_contrast'] : 0,
			'last_month' => $list[3]['register_contrast'] ? $list[3]['register_contrast'] : 0,
		];
		$register_spread = [
			'day' => $department_day['result']['registerNum']['day'] ? $department_day['result']['registerNum']['day'] : 0,
			'week' => $list[2]['register_contrast'] ? $list[2]['register_contrast'] : 0,
			'month' => $list[3]['register_contrast'] ? $list[3]['register_contrast'] : 0,
			'yesterday' => $list[1]['register_contrast'] ? $list[1]['register_contrast'] : 0,
			'last_week' => $list[2]['register_contrast'] ? $list[2]['register_contrast'] : 0,
			'last_month' => $list[3]['register_contrast'] ? $list[3]['register_contrast'] : 0,
		];
		
		$order_num = [
			'day' => $department_day['result']['orderNum']['day'] ? $department_day['result']['orderNum']['day'] : 0,
			'week' => $list[2]['order_num'] ? $list[2]['order_num'] : 0,
			'month' => $list[3]['order_num'] ? $list[3]['order_num'] : 0,
			'yesterday' => $list[1]['order_num_contrast'] ? $list[1]['order_num_contrast'] : 0,
			'last_week' => $list[2]['order_num_contrast'] ? $list[2]['order_num_contrast'] : 0,
			'last_month' => $list[3]['order_num_contrast'] ? $list[3]['order_num_contrast'] : 0,
		];
		
		$order_money = [
			'day' => $department_day['result']['orderPrice']['day'] ? $department_day['result']['orderPrice']['day'] : 0,
			'week' => $list[2]['order_amount'] ? $list[2]['order_amount'] : 0,
			'month' => $list[3]['order_amount'] ? $list[3]['order_amount'] : 0,
			'yesterday' => $list[1]['order_amount_contrast'] ? $list[1]['order_amount_contrast'] : 0,
			'last_week' => $list[2]['order_amount_contrast'] ? $list[2]['order_amount_contrast'] : 0,
			'last_month' => $list[3]['order_amount_contrast'] ? $list[3]['order_amount_contrast'] : 0,
		];
		$customer_num = [
			'day' => $department_day['result']['orderUser']['day'] ? $department_day['result']['orderUser']['day'] : 0,
			'week' => $list[2]['customer_num'] ? $list[2]['customer_num'] : 0,
			'month' => $list[3]['customer_num'] ? $list[3]['customer_num'] : 0,
			'yesterday' => $list[1]['customer_num_contrast'] ? $list[1]['customer_num_contrast'] : 0,
			'last_week' => $list[2]['customer_num_contrast'] ? $list[2]['customer_num_contrast'] : 0,
			'last_month' => $list[3]['customer_num_contrast'] ? $list[3]['customer_num_contrast'] : 0,
		];
		return $result = [
			'visit_clent' => $visit_clent,
			'register_num' => $register,
			'register_self' => $register,
			'orders_num' => $order_num,
			'orders_money' => $order_money,
			'customer_num' => $customer_num,
		];
	}
	/**
	 * 获取预存款订单金额
	 * @param  [type] $area       [省份]
	 * @param  [type] $city       [城市]
	 * @param  [type] $department [部门]
	 * @return [type]             [description]
	 */
	public function getDepositNum($area, $city, $department){
		if(!$area) {
			$this->setError('省份不能为空');
			return false;
		}
		if(!$department){
			$this->setError('部门不能为空');
			return false;
		}
		if($area == '全国'){
			$department_id = UserDepartment::find()
				->select(['id','is_select'])
				->where(['name' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
			$result = [];
			if(count($department_id) > 1){
				for($i = 0; $i < count($department_id); $i++){
					if(!$department[$i]['is_select']){
						unset($department[$i]['is_select']);
					}
					for($j = 1; $j < 4; $j ++) {
						$list[$i][$j] = $this->userRecord(30, $department_id[$i]['id'], 51, $j);
					}
				}
				for($i = 0; $i < count($list); $i++) {
					for($j  = 1; $j < 4; $j++) {
						
						$list[$j]['order_amount'] += $list[$i][$j]['order_amount']; 
						$list[$j]['order_amount_contrast'] += $list[$i][$j]['order_amount_contrast']; 
						
					}
				}
			}else {
				if(!$department_id[0]['is_select']){
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id[0]['id'], 51, $j);
					$list[$j] = $list[$j]['result'];
				}
			}
		}
		if($area != '全国') {
			if($city == '全部'){
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['province_name' => $area])
						->asArray()
						->all();
				for($i = 0; $i < count($domain_id); $i++) {
					$domain_id[$i] = $domain_id[$i]['city_id'];
				}
				$department_id = UserDepartment::find()
				->select(['id','is_select'])
				->where(['in','domain_id', $domain_id])
				->andWhere(['name' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
				if(!$department_id['is_select']) {
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id['id'],51, $j);
					$list[$j] = $list[$j]['result'];
				}
			}
			if($city != '全部') {
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['city_name' => $city])
						->asArray()
						->one();
				$department_id = UserDepartment::find()
						->select(['id', 'is_select'])
						->where(['domain_id' => $domain_id['city_id']])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				if(!$department_id) {
					$this->setError('该城市暂无相应部门');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id['id'],51, $j);
					$list[$j] = $list[$j]['result'];
				}
			}
		}
		$order_money = [
			'day' => $list[1]['order_amount'] ? $list[1]['order_amount'] : 0,
			'week' => $list[2]['order_amount'] ? $list[2]['order_amount'] : 0,
			'month' => $list[3]['order_amount'] ? $list[3]['order_amount'] : 0,
			'yesterday' => $list[1]['order_amount_contrast'] ? $list[1]['order_amount_contrast'] : 0,
			'last_week' => $list[2]['order_amount_contrast'] ? $list[2]['order_amount_contrast'] : 0,
			'last_month' => $list[3]['order_amount_contrast'] ? $list[3]['order_amount_contrast'] : 0,
		];
		return $result = [
			'orders_money' => $order_money,
		];
	}
	//买买金
	public function getPaymentNum($area, $city, $department)
	{
		if(!$area) {
			$this->setError('省份不能为空');
			return false;
		}
		if(!$department){
			$this->setError('部门不能为空');
			return false;
		}
		if($area == '全国'){
			$department_id = UserDepartment::find()
				->select(['id','is_select'])
				->where(['name' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
			$result = [];
			if(count($department_id) > 1){
				for($i = 0; $i < count($department_id); $i++){
					if(!$department[$i]['is_select']){
						unset($department[$i]['is_select']);
					}
					for($j = 1; $j < 4; $j ++) {
						$list[$i][$j] = $this->userRecord(30, $department_id[$i]['id'], 63, $j);
					}
				}
				for($i = 0; $i < count($department_id); $i++){
					if(!$department_id[$i]['is_select']){
						unset($department_id[$i]['is_select']);
					}
					$user_department[$i] = $this->departmentRecord($department_id[$i]['id']);
				}
				
				for($i = 0; $i < count($department_id); $i++) {
					$user_department[$i] = $department_id[$i]['id'];
				}
				$department_user = $this->departmentUser($user_department);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];

				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
				for($i = 0; $i < count($list); $i++) {
					for($j  = 1; $j < 4; $j++) {
						$list[$j]['order_num_contrast'] += $list[$i][$j]['result']['order_num_contrast']; 
						$list[$j]['order_amount'] += $list[$i][$j]['result']['order_amount']; 
						$list[$j]['order_amount_contrast'] += $list[$i][$j]['result']['order_amount_contrast']; 
						$list[$j]['order_num'] += $list[$i][$j]['result']['order_num']; 
						$list[$j]['customer_num'] += $list[$i][$j]['result']['customer_num']; 
						$list[$j]['customer_num_contrast'] += $list[$i][$j]['result']['customer_num_contrast']; 
					}
				}
			}else {
				if(!$department_id[0]['is_select']){
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id[$i]['id'], 63, $j);
					$list[$j] = $list[$j]['result'];
				}
				for($i = 0; $i < count($department_id); $i++) {
					$user_department[$i] = $department_id[$i]['id'];
				}
				$department_user = $this->departmentUser($user_department);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];

				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
			}
		}
		if($area != '全国') {
			if($city == '全部'){
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['province_name' => $area])
						->asArray()
						->all();
				for($i = 0; $i < count($domain_id); $i++) {
					$domain_id[$i] = $domain_id[$i]['city_id'];
				}
				$department_id = UserDepartment::find()
				->select(['id','is_select'])
				->where(['in','domain_id', $domain_id])
				->andWhere(['name' => $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
				if(!$department_id['is_select']) {
					$this->setError('该部门暂无数据');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id[$i]['id'], 63, $j);
					$list[$j] = $list[$j]['result'];
				}
				$department_user = $this->departmentUser($department_id['id']);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];
				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
			}
			if($city != '全部') {
				$domain_id = ProviceCity::find()
						->select(['city_id'])
						->where(['city_name' => $city])
						->asArray()
						->one();
				$department_id = UserDepartment::find()
						->select(['id', 'is_select'])
						->where(['domain_id' => $domain_id['city_id']])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				if(!$department_id) {
					$this->setError('该城市暂无相应部门');
					return false;
				}
				for($j = 1; $j < 4; $j ++) {
					$list[$j] = $this->userRecord(30, $department_id[$i]['id'], 63, $j);
					$list[$j] = $list[$j]['result'];
				}
				$department_user = $this->departmentUser($department_id['id']);
				for($i = 0; $i < count($department_user); $i++){
					$user_list[$i] = $department_user[$i]['username'];
					$user_list_id[$i] = $department_user[$i]['id'];
				}
				$department_day = $this->departmentDay($user_list);
				for($i = 0; $i < count($user_list); $i++){
					$visit[$i] = $this->visitData($user_list[$i]); 
				}
				for($i = 0; $i < count($visit); $i++) {
					$department_day['result']['visit'] += $visit[$i]['visitNum'];
				}
			}
		}
		
		$order_num = [
			'day' => $department_day['result']['orderThirdpartyNum']['day'] ? $department_day['result']['orderThirdpartyNum']['day'] : 0,
			'week' => $list[2]['order_num'] ? $list[2]['order_num'] : 0,
			'month' => $list[3]['order_num'] ? $list[3]['order_num'] : 0,
			'yesterday' => $list[1]['order_num_contrast'] ? $list[1]['order_num_contrast'] : 0,
			'last_week' => $list[2]['order_num_contrast'] ? $list[2]['order_num_contrast'] : 0,
			'last_month' => $list[3]['order_num_contrast'] ? $list[3]['order_num_contrast'] : 0,
		];
		
		$order_money = [
			'day' =>  $department_day['result']['orderThirdpartyPrice']['day'] ? $department_day['result']['orderThirdpartyPrice']['day'] : 0,
			'week' => $list[2]['order_amount'] ? $list[2]['order_amount'] : 0,
			'month' => $list[3]['order_amount'] ? $list[3]['order_amount'] : 0,
			'yesterday' => $list[1]['order_amount_contrast'] ? $list[1]['order_amount_contrast'] : 0,
			'last_week' => $list[2]['order_amount_contrast'] ? $list[2]['order_amount_contrast'] : 0,
			'last_month' => $list[3]['order_amount_contrast'] ? $list[3]['order_amount_contrast'] : 0,
		];
		$customer_num = [
			'day' => $department_day['result']['orderThirdpartyUser']['day'] ? $department_day['result']['orderThirdpartyUser']['day'] : 0,
			'week' => $list[2]['customer_num'] ? $list[2]['customer_num'] : 0,
			'month' => $list[3]['customer_num'] ? $list[3]['customer_num'] : 0,
			'yesterday' => $list[1]['customer_num_contrast'] ? $list[1]['customer_num_contrast'] : 0,
			'last_week' => $list[2]['customer_num_contrast'] ? $list[2]['customer_num_contrast'] : 0,
			'last_month' => $list[3]['customer_num_contrast'] ? $list[3]['customer_num_contrast'] : 0,
		];
		return $result = [
			'orders_num' => $order_num,
			'orders_money' => $order_money,
			'customer_num' => $customer_num,
		];
	}
	//获取部门所有人员接口
	private function departmentUser($department)
	{

		if(is_array($department)){
			$group = UserGroup::find()
					->select(['id','domain_id'])
					->where(['in', 'department_id', $department])
					->asArray()
					->all();
			for($i = 0; $i < count($group); $i++){
				$group_id['id'][$i] = $group[$i]['id']; 
				$group_id['domain_id'][$i] = $group[$i]['domain_id']; 
			}
			$list = User::find()
					->select(['id','username'])
					->where(['in', 'department_id', $department])
					->andWhere(['in', 'domain_id', $group_id['domain_id']])
					->asArray()
					->all();
		}
		if(is_string($department)){
			$group = UserGroup::find()
					->select(['id', 'domain_id'])
					->where(['department_id' => $department])
					->asArray()
					->all();
			for($i = 0; $i < count($group); $i++){
				$group_id['id'][$i] = $group[$i]['id'];
				$group_id['domain_id'][$i] = $group[$i]['domain_id'];  
			}
			$list = User::find()
					->select(['id','username'])
					->where(['department_id' => $department])
					->andWhere(['in', 'domain_id', $group_id['domain_id']])
					->asArray()
					->all();
		}
		return $list;
	}
	//调取个人实时数据接口
	private function departmentDay($user)
	{
		
		$data = [
			'user' => json_encode($user),
		];
		
		$list = \Yii::$app->api->request($this->user_api,$data);
		return $list;
	}
	//调取计划任务接口
	private function userRecord($type, $type_id, $payment, $period) {
		$data = [
				'type' => $type,
				'type_id' => $type_id,
				'payment' => $payment,
				'period' => $period,
				'order_status' => 1,
			];
		$user_record = \Yii::$app->api->request($this->api,$data);
		if(!$user_record) {
			$this->setError($user_record['msg']);
			return false;
		}
		return $user_record;
	}
	//根据部门ID 查询部门成员
	private function departmentRecord($department)
	{
		
		$list = User::find()
				->select(['id'])
				->where(['department_id' => $department])
				->asArray()
				->all();
		return $list;
	}
	//调取员工业务记录接口
	private function userDepartment($user_id, $num)
	{
		$type = 0;
		$user = $user_id;
		$data = [
			'num' => $num,
			'user' => $user,
			'type' => $type,
		];
		$list = \Yii::$app->api->request($this->department_api,$data);
		return $list;
	}
	/**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private function visitData($user)
    {
    	$start = mktime(0,0,0,date('m'),date('d'),date('Y'));
    	$end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        if(is_array($user)){
            $user = implode(',', $user);
        }
       
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere('user in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->one(\Yii::$app->dbofficial);
    
        $nums = UserBusinessNotes::find()
        ->andWhere('staff_num in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->count();
        $row = array();
        $row['visitNum'] = $rows['visitNum']+$nums;
        return $row;
    }
}