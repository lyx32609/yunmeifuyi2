<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\UploadUserInfoNewService;
class UploadUserInfoNewApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post ('user_id');
		$name = \Yii::$app->request->post ('name');
		$rank = \Yii::$app->request->post ('rank');
		$item_name = \Yii::$app->request->post ('item_name');
		$password = \Yii::$app->request->post ('password');
		$phone = \Yii::$app->request->post ('phone');
		$group_id = \Yii::$app->request->post ('group_id');
		$department = \Yii::$app->request->post ('department');
		$staffId = \Yii::$app->request->post ('staff_id');
		$service = UploadUserInfoNewService::instance ();
		$result = $service->uploadUserInfo($user_id, $name, $password, $rank, $item_name, $phone, $group_id, $department, $staffId);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 