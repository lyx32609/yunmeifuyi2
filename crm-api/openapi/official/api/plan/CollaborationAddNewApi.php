<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\CollaborationAddNewService;

class CollaborationAddNewApi extends Api
{
	public function run()
	{
		$problem_id = \Yii::$app->request->post('problem_id');
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$collaboration_content = \Yii::$app->request->post('collaboration_content');
		$appid = \Yii::$app->request->post('gtid');
        $appkey = \Yii::$app->request->post('gtkey');
        $masterSecret = \Yii::$app->request->post('gtmaster');
		$service = CollaborationAddNewService::instance();
		$result = $service->questionAdd($problem_id, $user_id, $user_name, $collaboration_content, $appkey, $appid, $masterSecret);

		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];

	}
}