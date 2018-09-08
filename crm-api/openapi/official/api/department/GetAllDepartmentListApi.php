<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetAllDepartmentListService;

class GetAllDepartmentListApi extends Api
{
	public function run()
	{
		$companyId= \Yii::$app->request->post('companyId');//公司ID
		$service = GetAllDepartmentListService::instance();
		$result = $service->getDepartmentList($companyId);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}