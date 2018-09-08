<?php
namespace official\api\orders;

use app\foundation\Api;
use app\services\GetGoodsInfoNewService;
class AddGoodsNewApi extends Api
{
	public function run()
	{
		$goodsId = \Yii::$app->request->post('goodsId');
		$memberId = \Yii::$app->request->post('memberId');
		$num = \Yii::$app->request->post('num');
		$user_id = \Yii::$app->request->post('user_id');
		$company_id = \Yii::$app->request->post('company_id');
		$goods_company = \Yii::$app->request->post('goods_company');
		$goods_name = \Yii::$app->request->post('goods_name');
		$is_cooperation = \Yii::$app->request->post('is_cooperation');
		$orders_money = \Yii::$app->request->post('orders_money');
		$barcode = \Yii::$app->request->post('barcode');
		$service = GetGoodsInfoNewService::instance();
		$result = $service->addGoods($barcode,$goodsId, $memberId, $num, $user_id, $company_id, $goods_company, $goods_name, $is_cooperation, $orders_money);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}
