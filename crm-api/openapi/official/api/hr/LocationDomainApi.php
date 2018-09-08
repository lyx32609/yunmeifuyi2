<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\LocationDepartmentService;
use app\models\UserLocation;
use app\models\UserDomain;
class LocationDomainApi extends Api
{
	/**
	 * 循环更新组地区id
	 * @return [type] [description]
	 */
	public function run()
	{
	    $start = \Yii::$app->request->post ('start');
	    $end = \Yii::$app->request->post ('end');
		$service = LocationDepartmentService::instance ();
		$result = $service->locationDepartment($start, $end);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
	
} 