<?php
namespace official\api\petition;

use app\foundation\Api;
use app\services\GetApproverService;

class GetApproverApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');

		$service = GetApproverService::instance();
		$result = $service->GetApprover($user_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg'=>$result];
	}
}
