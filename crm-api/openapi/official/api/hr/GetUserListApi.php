<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetUserListService;
class GetUserListApi extends Api
{
	public function run()
	{
		$department = \Yii::$app->request->post ('department');
		$service = GetUserListService::instance ();
		$result = $service->getUsertList($department);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 