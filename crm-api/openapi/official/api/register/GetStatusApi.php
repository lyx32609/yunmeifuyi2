<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetStatusService;

class GetStatusApi extends Api
{
	public function run()
	{
		$service = GetStatusService::instance();
		$result = $service->getStatus();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}