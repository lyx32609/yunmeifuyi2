<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessProblemInfoService;

class GetBusinessProblemInfoApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$problem_lock = \Yii::$app->request->post('problem_lock');
		$type = \Yii::$app->request->post('type');
		$service = GetBusinessProblemInfoService::instance();
		$result = $service->getQueryInfo($user_id, $problem_lock, $type);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}
