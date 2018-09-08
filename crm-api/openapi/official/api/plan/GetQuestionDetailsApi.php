<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\GetQusetionDetailsService;

class GetQuestionDetailsApi extends Api
{
	public function run()
	{
		$problem_id = \Yii::$app->request->post('problem_id');
		$type = \Yii::$app->request->post('type');

		$service = GetQusetionDetailsService::instance();
		$result = $service->getQusetionDetails($problem_id, $type);
		if($result === false)
        {
            return $this->logicError($service->error);
        }

		return ['msg' => $result];
	}
}