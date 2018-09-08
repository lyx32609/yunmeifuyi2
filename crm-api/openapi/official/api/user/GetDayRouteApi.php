<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\GetDayRouteService;


class GetDayRouteApi extends Api
{
    public function run()
    {

        $user = \Yii::$app->request->post('user_id');
        $date = \Yii::$app->request->post('date');
        $service = GetDayRouteService::instance();
        $result = $service->getUserRoute($user,$date);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $result;
    }
}