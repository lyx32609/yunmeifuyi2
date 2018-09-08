<?php
namespace official\api\submen;

use app\foundation\Api;
use app\services\GetSubmenuService;


class GetSubmenuApi extends Api
{
    public function run()
    {
       $user_id = \Yii::$app->request->post('user_id');
       $area = \Yii::$app->request->post('area');
       $city = \Yii::$app->request->post('city');
       $department = \Yii::$app->request->post('department');
       $specification = \Yii::$app->request->post('specification');
        $service = GetSubmenuService::instance();
        $result =$service->getSubmenu($user_id, $area, $city, $department, $specification);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return $result;
        
    }
}