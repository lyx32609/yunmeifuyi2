<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetCityDepartmentListAllService;
class GetCityDepartmentListAllApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post ('city');
		$department = \Yii::$app->request->post ('department');
		$company_category_id = \Yii::$app->request->post('company_category_id');
		$service = GetCityDepartmentListAllService::instance ();
		$result = $service->getCityDepartmentList($city, $department,$company_category_id);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		if(isset($result['ret'])&&$result['ret']==28)
        {
            return $result;
        }
		return ['msg' => $result];
	}
}