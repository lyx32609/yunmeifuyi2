<?php
namespace official\api\orders;

use app\foundation\Api;
use app\services\GetGoodsInfoService;

/**
 * 根据条形码和采购商,获取商品信息
 */
class GetGoodsInfoApi extends Api
{
	public function run()
	{
		$barCord = \Yii::$app->request->post('barCord');
		$memberId = \Yii::$app->request->post('memberId');
		
		$service = GetGoodsInfoService::instance();
		$result = $service->getGoodsBarCord($barCord, $memberId);
		if($result === false) {
			
			return $this->logicError($service->error, $service->errors);
		}

		return ['msg' => $result];
	}
}


