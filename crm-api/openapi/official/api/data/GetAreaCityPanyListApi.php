<?php
namespace official\api\data;

use app\foundation\Api;
use app\services\GetAreaCityPanyListService;

class GetAreaCityPanyListApi extends Api
{
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$company_category_id = \Yii::$app->request->post('company_category_id');
		$rank = \Yii::$app->request->post('rank');
		$fly = \Yii::$app->request->post('fly');
		$area = \Yii::$app->request->post('area');
		$city = \Yii::$app->request->post('city');
		$type = \Yii::$app->request->post('type');
		$company = \Yii::$app->request->post('company');
		$status = \Yii::$app->request->post('status');
		$service = GetAreaCityPanyListService::instance();
		$result = $service->getAreaCityPanyList($user_id, $company_category_id, $rank, $fly, $area, $city, $company, $type, $status);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}