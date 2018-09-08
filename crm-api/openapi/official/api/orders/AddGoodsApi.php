<?php
namespace official\api\orders;

use app\foundation\Api;
use app\services\GetGoodsInfoService;
class AddGoodsApi extends Api
{
	public function run()
	{
		$goodsId = \Yii::$app->request->post('goodsId');
		$memberId = \Yii::$app->request->post('memberId');
		$num = \Yii::$app->request->post('num');
		$service = GetGoodsInfoService::instance();
		$result = $service->addGoods($goodsId, $memberId, $num);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}
