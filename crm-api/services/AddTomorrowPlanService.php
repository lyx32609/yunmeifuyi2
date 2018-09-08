<?php
namespace app\services;

use app\foundation\Service;
use app\models\TomorrowPlan;
/**
 * 生成明日计划
 */

class AddTomorrowPlanService extends Service
{
	/**
	 * [addTomorrowPlan description]
	 * @param [type] $user_id         用户ID
	 * @param [type] $user_name       用户名
	 * @param [type] $visit_clent     拜访客户
	 * @param [type] $register_num    注册数量
	 * @param [type] $register_self   自己注册
	 * @param [type] $register_spread 传播注册
	 * @param [type] $orders_num      订单数量
	 * @param [type] $orders_money    订单金额
	 * @param [type] $pre_deposit     预存款
	 * @param [type] $pre_money       预存款金额
	 * @param [type] $specification   规格
	 * @param [type] $remarks         备注(默认为null)
	 */
	public function addTomorrowPlan( $user_id, $user_name, $visit_clent, $register_num, $register_self, $register_spread, $orders_num, $orders_money, $pre_deposit, $pre_money,$specification, $remarks = null)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$user_name) {
			$this->setError('用户名不能为空');
			return false;
		}
		if(!$visit_clent) {
			$this->setError('拜访客户不能为空');
			return false;
		}
		if(!$register_num) {
			$this->setError('注册数量不能为空');
			return false;
		}
		if(!$register_self) {
			$this->setError('自己注册不能为空');
			return false;
		}
		if(!$register_spread) {
			$this->setError('传播注册不能为空');
			return false;
		}
		if(!$orders_num) {
			$this->setError('订单数量不能为空');
			return false;
		}
		if(!$orders_money) {
			$this->setError('订单金额不能为空');
			return false;
		}
		// if(!$pre_deposit) {
		// 	$this->setError('预存款不能为空');
		// 	return false;
		// }
		// if(!$pre_money) {
		// 	$this->setError('预存款金额不能为空');
		// 	return false;
		// }	
		if(!$specification) {
			$this->setError('规格不能为空');
			return false;
		}
		
		if($specification == 1){
			$startTime = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endTime = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		}
		if($specification == 2){
			$startTime = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
			$endTime = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
		}
		if($specification == 3){
			$startTime = mktime(0, 0 , 0,date("m"),1,date("Y"));
			$endTime =  mktime(23,59,59,date("m"),date("t"),date("Y"));
		}
		$data = ['plan_id'];
		$data = TomorrowPlan::find()
					->select($data)
					->where(['user_id' => $user_id])
					->andWhere(['specification' => $specification])
					->andWhere(['between', 'create_time', $startTime, $endTime])
					->asArray()
					->one();
		if($data) {
			$this->setError('计划已存在');
			return false;
		}
		$result = new TomorrowPlan;
		$result->user_id = $user_id;
		$result->user_name = $user_name;
		$result->visit_clent = $visit_clent;
		$result->register_num = $register_num;
		$result->register_self = $register_self;
		$result->register_spread = $register_spread;
		$result->orders_num = $orders_num;
		$result->orders_money = $orders_money;
		// $result->pre_deposit = $pre_deposit;
		// $result->pre_money = $pre_money;
		$result->create_time = time();
		$result->remarks = $remarks;
		$result->specification = $specification;
		$result->save();

		if(!$result) {
			$this->setError('明日计划创建失败');
			return false;
		} 
			return $result = '明日计划创建成功';
		
	}
}