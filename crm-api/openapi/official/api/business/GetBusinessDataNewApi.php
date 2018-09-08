<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessDataNewService;


class GetBusinessDataNewApi extends Api
{
    public function run()
    {
       $area = \Yii::$app->request->post('area');
       $city = \Yii::$app->request->post('city');
       $department = \Yii::$app->request->post('department');
        $service = GetBusinessDataNewService::instance();
        $result =$service->getBusinessData($area, $city, $department);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
        
    }
}