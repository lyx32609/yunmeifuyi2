<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetProductListService;

class GetProductListApi extends Api
{
	public function run()
	{
		$service = GetProductListService::instance();
		$result = $service->getProductList();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}