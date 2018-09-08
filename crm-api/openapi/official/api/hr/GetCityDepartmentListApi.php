<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\GetCityDepartmentListService;
class GetCityDepartmentListApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post ('city');
		$department = \Yii::$app->request->post ('department');
		$service = GetCityDepartmentListService::instance ();
		$result = $service->getCityDepartmentList($city, $department);
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