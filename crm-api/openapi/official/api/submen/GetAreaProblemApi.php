<?php
namespace official\api\submen;

use app\foundation\Api;
use app\services\GetAreaProblemService;


class GetAreaProblemApi extends Api
{
    public function run()
    {
       $user_id = \Yii::$app->request->post('user_id');
       $area = \Yii::$app->request->post('area');
       $city = \Yii::$app->request->post('city');
       $department = \Yii::$app->request->post('department');
       $startTime = \Yii::$app->request->post('startTime');
       $endTime = \Yii::$app->request->post('endTime');
        $service = GetAreaProblemService::instance();
        $result =$service->getAreaProblem($user_id, $area, $city, $department, $startTime, $endTime);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return $result;
        
    }
}