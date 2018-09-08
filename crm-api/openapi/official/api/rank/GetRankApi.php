<?php
namespace official\api\rank;

use app\foundation\Api;
use app\services\GetRankService;

class GetRankApi extends Api
{
	public function run()
	{
		$service = GetRankService::instance();
		$result = $service->getRank();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}