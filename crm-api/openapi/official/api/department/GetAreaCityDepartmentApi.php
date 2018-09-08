<?php
namespace official\api\department;

use app\foundation\Api;
use app\services\GetAreaCityDepartmentListService;

class GetAreaCityDepartmentApi extends Api
{
	public function run()
	{
		$company_category_id = \Yii::$app->request->post('company_category_id');//公司ID
		$user_id = \Yii::$app->request->post('user_id');//登录人id
		$service = GetAreaCityDepartmentListService::instance();
		$result = $service->getList($company_category_id,$user_id);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}