<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessDataFourService;


class GetBusinessDataFourApi extends Api
{
    public function run()
    {
        $company_categroy_id = \Yii::$app->request->post('company_categroy_id');
        $area_id = \Yii::$app->request->post('area_id');
        $department_id = \Yii::$app->request->post('department_id');
        $city_id = \Yii::$app->request->post('city_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
       	$num = \Yii::$app->request->post('num');
        $user_id = \Yii::$app->request->post('user_id');
        $department_name = \Yii::$app->request->post('department_name');
        $service = GetBusinessDataFourService::instance();
        $result = $service->getData($company_categroy_id, $area_id, $department_id, $city_id, $is_cooperation, $num, $user_id, $department_name);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
        
    }
}