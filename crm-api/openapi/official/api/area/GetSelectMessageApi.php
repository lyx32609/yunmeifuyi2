<?php
namespace official\api\area;

use app\foundation\Api;
use app\services\GetSelectMessageService;

class GetSelectMessageApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$area = \Yii::$app->request->post('area');
		$city = \Yii::$app->request->post('city');
		$department = \Yii::$app->request->post('department');
		$service = GetSelectMessageService::instance();
		$result = $service->getSelectMessage($user_id, $area, $city, $department);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}