<?php
namespace official\api\orders;

use app\foundation\Api;
use app\services\GetGoodsInfoNewService;

/**
 * 根据条形码和采购商,获取商品信息
 */
class GetGoodsInfoNewApi extends Api
{
	public function run()
	{
		$barCord = \Yii::$app->request->post('barCord');
		$memberId = \Yii::$app->request->post('memberId');
		$companyId = \Yii::$app->request->post('companyId');
		$is_cooperation = \Yii::$app->request->post('is_cooperation');
		$company_categroy_id = \Yii::$app->request->post('company_categroy_id');
		$service = GetGoodsInfoNewService::instance();
		$result = $service->getGoodsBarCord($barCord, $memberId, $companyId, $is_cooperation, $company_categroy_id);
		if($result === false) {
			
			return $this->logicError($service->error, $service->errors);
		}

		return ['msg' => $result];
	}
}


