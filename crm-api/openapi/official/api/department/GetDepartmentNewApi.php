<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetDepartmentInfoService;

class GetDepartmentNewApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$company_category_id = \Yii::$app->request->post('company_category_id');
		$service = GetDepartmentInfoService::instance();
		$result = $service->getDepartmentNew($user_id, $company_category_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}