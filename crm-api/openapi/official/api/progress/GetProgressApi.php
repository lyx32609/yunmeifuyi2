<?php
namespace official\api\progress;

use app\foundation\Api;
use app\services\GetTodayProgressService;

class GetProgressApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$type = \Yii::$app->request->post('type');
		$service = GetTodayProgressService::instance();
		$result = $service->getUserProgress($user_id, $type);
		if($result === false)
        {
            return $this->logicError($service->error);
        } 
		return ['msg' => $result];
	}
}