<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\CollaborationAddService;

class CollaborationAddApi extends Api
{
	public function run()
	{
		$problem_id = \Yii::$app->request->post('problem_id');
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$collaboration_content = \Yii::$app->request->post('collaboration_content');

		$service = CollaborationAddService::instance();
		$result = $service->questionAdd($problem_id, $user_id, $user_name, $collaboration_content);

		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];

	}
}