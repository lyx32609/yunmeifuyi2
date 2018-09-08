<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetGroupInfoService;

class GetGroupInfoApi extends Api
{
	public function run()
	{
		$department = \Yii::$app->request->post ('department');
		$service = GetGroupInfoService::instance ();
		$result = $service->getGroupInfo($department);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
}