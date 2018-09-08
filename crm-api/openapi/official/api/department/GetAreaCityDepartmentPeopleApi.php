<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetAreaCityDepartmentPeopleService;

class GetAreaCityDepartmentPeopleApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post('city');
		$service = GetAreaCityDepartmentPeopleService::instance();
		$result = $service->getAreaCityDepartmentPeople($city);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}