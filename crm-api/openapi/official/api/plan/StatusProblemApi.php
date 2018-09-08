<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\StatusProblemService;
class StatusProblemApi extends Api
{
	/**
	 * 提交问题接口
	 * 
	 */
	public function run()
	{

		$user_id = \Yii::$app->request->post('user_id');
		$status_id = \Yii::$app->request->post('status_id');
		$status = \Yii::$app->request->post('status');
		
		$service = StatusProblemService::instance();
		$result = $service->statusProblem($user_id, $status_id, $status);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}