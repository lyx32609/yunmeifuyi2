<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetDepartmentListService;

class GetDepartmentListApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post ('user_id');
		$service = GetDepartmentListService::instance ();
		$result = $service->getDepartmentList($user_id);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
}