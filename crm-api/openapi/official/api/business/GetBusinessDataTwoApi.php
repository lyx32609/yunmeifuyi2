<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessDataTwoService;


class GetBusinessDataTwoApi extends Api
{
    public function run()
    {
        $company_id = \Yii::$app->request->post('company_id');
       	$area = \Yii::$app->request->post('area');
       	$city = \Yii::$app->request->post('city');
       	$department = \Yii::$app->request->post('department');
       	$num = \Yii::$app->request->post('num');
        $service = GetBusinessDataTwoService::instance();
        $result =$service->getData($company_id, $area, $city, $department, $num);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
        
    }
}