<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessDataNumService;


class GetBusinessDataNumApi extends Api
{
    public function run()
    {
       	$area = \Yii::$app->request->post('area');
       	$city = \Yii::$app->request->post('city');
       	$department = \Yii::$app->request->post('department');
       	$num = \Yii::$app->request->post('num');
        $service = GetBusinessDataNumService::instance();
        $result =$service->getBusinessData($area, $city, $department, $num);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
        
    }
}