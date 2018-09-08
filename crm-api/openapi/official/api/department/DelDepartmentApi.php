<?php

namespace official\api\department;

use app\foundation\Api;
use app\services\SaveDepartmentService;
class DelDepartmentApi extends Api
{
	public function run()
	{
		$companyId = \Yii::$app->request->post ('companyId');//企业ID
		$id = \Yii::$app->request->post ('id');//部门ID
		$service = SaveDepartmentService::instance ();
		$result = $service->delDepartment($companyId,$id);

		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 