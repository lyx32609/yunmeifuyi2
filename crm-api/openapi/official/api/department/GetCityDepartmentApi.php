<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetDepartmentInfoService;

class GetDepartmentInfoApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post('city');
		$service = GetCityDepartmentService::instance();
		$result = $service->getCityDepartment();
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}