<?php
namespace official\api\rank;

use app\foundation\Api;
use app\services\GetRankListService;

class GetRankListApi extends Api
{
	public function run()
	{
		$city = \Yii::$app->request->post('city');
		$area = \Yii::$app->request->post('area');
		$department = \Yii::$app->request->post('department');
		$rank = \Yii::$app->request->post('rank');
		$specification = \Yii::$app->request->post('specification');
		$service = GetRankListService::instance();
		$result = $services->getRankList($area, $city, $department, $rank, $specification);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}