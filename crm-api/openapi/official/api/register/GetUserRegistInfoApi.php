<?php
namespace official\api\register;

use app\foundation\Api;
use app\services\GetUserRegistInfoService;

class GetUserRegistInfoApi extends Api
{
	public function run()
	{
		$start_time = \Yii::$app->request->post('start_time');
		$end_time = \Yii::$app->request->post('end_time');
		$service = GetUserRegistInfoService::instance();
		$result = $service->showUserInfo($start_time,$end_time);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}