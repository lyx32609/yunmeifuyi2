<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\BindCarService;


class BindCarsApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $service = BindCarsService::instance();
        $data = $service->bundCars($user_id);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        } 
        return ['msg' => $data];
        
    }
}