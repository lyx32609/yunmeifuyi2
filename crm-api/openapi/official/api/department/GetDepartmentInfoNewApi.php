<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetDepartmentInfoService;

class GetDepartmentInfoNewApi extends Api
{
	public function run()
	{
		$is_cooperation = \Yii::$app->request->post('is_cooperation');
		$user_id = \Yii::$app->request->post('user_id');
		$service = GetDepartmentInfoService::instance();
		$result = $service->getDepartmentInfoNew($user_id, $is_cooperation);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}