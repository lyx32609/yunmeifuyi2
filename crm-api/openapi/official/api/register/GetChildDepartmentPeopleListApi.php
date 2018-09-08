<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetChildDepartmentPeopleListService;

class GetChildDepartmentPeopleListApi extends Api
{
	public function run()
	{
		$department_id = \Yii::$app->request->post('department_id');
		$type = \Yii::$app->request->post('type');
		$service = GetChildDepartmentPeopleListService::instance();
		$result = $service->getChildDepartmentPeopleList($department_id, $type);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}
