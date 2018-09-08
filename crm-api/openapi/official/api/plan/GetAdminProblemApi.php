<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\AddProblemService;

class GetAdminProblemApi extends Api
{
	public function run()
	{
		
		$user_id = \Yii::$app->request->post('user_id');
		$problem_lock = \Yii::$app->request->post('problem_lock');
		
		$service = AddProblemService::instance();
		$result = $service->getAdminProblem($user_id, $problem_lock);
		
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}
