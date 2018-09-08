<?php

namespace official\api\department;

use app\foundation\Api;
use app\services\SaveDepartmentService;
class EditChildDepartmentApi extends Api
{
	public function run()
	{
		$depart_id = \Yii::$app->request->post ('depart_id');
		$companyId = \Yii::$app->request->post ('companyId');//企业ID
		$name = \Yii::$app->request->post ('name');
		$service = SaveDepartmentService::instance ();
		$result = $service->editChildDepart($depart_id,$companyId,$name);
		
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 