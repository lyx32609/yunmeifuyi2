<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetAreaCityDepartmentPeopleNewService;

class GetAreaCityDepartmentPeopleNewApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post('city');
		$company_category_id = \Yii::$app->request->post('company_category_id');
		$service = GetAreaCityDepartmentPeopleNewService::instance();
		$result = $service->getAreaCityDepartmentPeople($city,$company_category_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}