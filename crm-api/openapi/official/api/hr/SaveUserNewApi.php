<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\SaveUserNewService;
class SaveUserNewApi extends Api
{
	public function run()
	{
		$name = \Yii::$app->request->post ('name');
		$rank = \Yii::$app->request->post ('rank');
		$item_name = \Yii::$app->request->post ('item_name');
		$username = \Yii::$app->request->post ('username');
		$phone = \Yii::$app->request->post ('phone');
		$group_id = \Yii::$app->request->post ('group_id');
		$password = \Yii::$app->request->post ('password');
		$department = \Yii::$app->request->post ('department');
		$domain = \Yii::$app->request->post ('domain');
		$staffId = \Yii::$app->request->post ('staff_id');
		$company_category_id = \Yii::$app->request->post ('company_category_id');
		$service = SaveUserNewService::instance ();
		$result = $service->saveUser($name, $rank, $item_name, $username, $phone, $group_id,$department, $password, $domain,$staffId, $company_category_id);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 