<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetAreaCityService;

class GetAreaCityApi extends Api
{
	public function run()
	{
		$service = GetAreaCityService::instance();
		$result = $service->getAreaCity();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}