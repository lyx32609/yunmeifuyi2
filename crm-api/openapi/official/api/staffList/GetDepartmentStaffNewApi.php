<?php
namespace official\api\staffList;

use app\foundation\Api;
use app\services\GetDepartmentStaffNewService;

class GetDepartmentStaffNewApi extends Api
{
	public function run()
	{
	    $city = \Yii::$app->request->post('city');
	    $department = \Yii::$app->request->post('department');
	    $company_category_id = \Yii::$app->request->post('company_category_id');
	    $service = GetDepartmentStaffNewService::instance();
	    $result = $service->getDepartmentStaff($city, $department, $company_category_id);
	    if($result === false)
	    {
	        return $this->logicError($service->error);
	    }
	    return ['msg' => $result];
	}
}