<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetProductListService;

class GetServiceListApi extends Api
{
	public function run()
	{
		$service = GetProductListService::instance();
		$result = $service->getServiceList();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}