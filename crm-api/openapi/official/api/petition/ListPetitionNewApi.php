<?php

namespace official\api\petition;
use app\foundation\Api;
use app\services\PetitionNewService;


/**
 * 员工个人签呈列表接口
 */
class ListPetitionNewApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$page_count = \Yii::$app->request->post('page_count');
		$page_size = \Yii::$app->request->post('page_size');

		$service = PetitionNewService::instance();
		$result = $service->listPetition($user_id, $page_count,$page_size);

		if ($result === false) {
			return $this->logicError($service->error);
		}
		return ['msg'=>$result];
	}
}