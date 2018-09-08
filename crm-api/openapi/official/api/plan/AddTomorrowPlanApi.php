<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\AddTomorrowPlanService;

class AddTomorrowPlanApi extends Api
{
	public function run ()
	{

		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$visit_clent = \Yii::$app->request->post('visit_clent');
		$register_num = \Yii::$app->request->post('register_num');
		$register_self = \Yii::$app->request->post('register_self');
		$register_spread = \Yii::$app->request->post('register_spread');
		$orders_num = \Yii::$app->request->post('orders_num');
		$orders_money = \Yii::$app->request->post('orders_money');
		$pre_deposit = \Yii::$app->request->post('pre_deposit');
		$pre_money = \Yii::$app->request->post('pre_money');
		$specification = \Yii::$app->request->post('specification');
		$remarks = \Yii::$app->request->post('remarks');

		$service = AddTomorrowPlanService::instance();

		$result = $service->addTomorrowPlan($user_id, $user_name, $visit_clent, $register_num, $register_self, $register_spread, $orders_num, $orders_money, $pre_deposit, $pre_money,  $specification, $remarks);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}