<?php

namespace official\api\petition;
use app\foundation\Api;
use app\services\ListPetitionService;


/**
 * 员工个人签呈列表接口
 */
class ListPetitionApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$page_count = \Yii::$app->request->post('page_count');
		$page_size = \Yii::$app->request->post('page_size');

		$service = ListPetitionService::instance();
		$result = $service->listPetition($user_id, $page_count,$page_size);

		if ($result === false) {
			return $this->logicError($service->error);
		}
		return ['msg'=>$result];
	}
}