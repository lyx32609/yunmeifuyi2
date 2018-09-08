<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetDepartmentInfoService;

class GetDepartmentInfoApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$service = GetDepartmentInfoService::instance();
		$result = $service->getDepartmentInfo($user_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}