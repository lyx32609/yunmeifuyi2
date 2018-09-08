<?php

namespace official\api\department;

use app\foundation\Api;
use app\services\SaveDepartmentService;
class SaveDepartmentApi extends Api
{
	public function run()
	{
		$name = \Yii::$app->request->post ('name');//部门名字
		$is_select= \Yii::$app->request->post ('is_select');//是否统计
		$childs = \Yii::$app->request->post ('childs');
		$companyId = \Yii::$app->request->post ('companyId');//企业ID
		$domain_id = \Yii::$app->request->post ('domain_id');//区域ID
		$id = \Yii::$app->request->post ('id');//部门ID
		$service = SavedepartmentService::instance ();
		$result = $service->saveDepartment($name,$is_select,$childs,$companyId,$domain_id,$id);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 