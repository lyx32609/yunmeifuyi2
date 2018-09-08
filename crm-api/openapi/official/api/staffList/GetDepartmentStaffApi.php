<?php
namespace official\api\staffList;

use app\foundation\Api;
use app\services\GetDepartmentStaffService;

class GetDepartmentStaffApi extends Api
{
	public function run()
	{
	    $city = \Yii::$app->request->post('city');
	    $department = \Yii::$app->request->post('department');
	    $service = GetDepartmentStaffService::instance();
	    $result =$service->getDepartmentStaff($city, $department);
	    if($result === false)
	    {
	        return $this->logicError($service->error);
	    }
	    return ['msg' => $result];
	}
}