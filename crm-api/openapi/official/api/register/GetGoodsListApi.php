<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetProductListService;

class GetGoodsListApi extends Api
{
	public function run()
	{
		$service = GetProductListService::instance();
		$result = $service->getGoodsList();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}