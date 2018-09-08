<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\AddProblemService;

class GetQueryInfoApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$problem_lock = \Yii::$app->request->post('problem_lock');
		$type = \Yii::$app->request->post('type');
		$service = AddProblemService::instance();
		$result = $service->getQueryInfo($user_id, $problem_lock, $type);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}
