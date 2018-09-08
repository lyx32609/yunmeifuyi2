<?php
namespace official\api\petitionNew;

use app\foundation\Api;
use app\services\GetApproverNewService;

/**
 * Class GetApproverNewApi
 * 获取审批人（加签版本）
 * @package official\api\petitionNew
 */
class GetApproverNewApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');

		$service = GetApproverNewService::instance();
		$result = $service->GetApprover($user_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg'=>$result];
	}
}
