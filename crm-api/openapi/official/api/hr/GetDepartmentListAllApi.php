<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetDepartmentListAllService;

class GetDepartmentListAllApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post ('user_id');
		$company_category_id = \Yii::$app->request->post('company_category_id');
		$service = GetDepartmentListAllService::instance ();
		$result = $service->getDepartmentList($user_id,$company_category_id);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
}