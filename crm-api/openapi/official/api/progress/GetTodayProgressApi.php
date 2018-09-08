<?php
namespace official\api\progress;

use app\foundation\Api;
use app\services\GetTodayProgressService;

class GetTodayProgressApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$specification = \Yii::$app->request->post('specification');
		$is_cooperation = \Yii::$app->request->post('is_cooperation');
		$service = GetTodayProgressService::instance();
		$result = $service->getTodayProgress($user_id, $specification, $is_cooperation);
		if($result === false)
        {
            return $this->logicError($service->error);
        } 
		return ['msg' => $result];
	}
}