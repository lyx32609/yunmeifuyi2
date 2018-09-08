<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetUserListNewService;
class GetUserListNewApi extends Api
{
	public function run()
	{
		$department = \Yii::$app->request->post ('department');
		$service = GetUserListNewService::instance ();
		$result = $service->getUsertList($department);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 



