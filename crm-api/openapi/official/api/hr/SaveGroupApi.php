<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\SaveGroupService;
class SaveGroupApi extends Api
{
	public function run()
	{
		$department = \Yii::$app->request->post ('department');
		$name = \Yii::$app->request->post ('name');
		$domain = \Yii::$app->request->post ('domain');
		$service = SaveGroupService::instance ();
		$result = $service->saveGroup($department, $name, $domain);
		if ($result === false) {
			return $this->logicError( $service->error, $service->errors );
		}
		return ['msg' => $result];
	}
} 