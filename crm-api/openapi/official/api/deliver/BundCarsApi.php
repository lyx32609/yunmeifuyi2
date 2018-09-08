<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\BindCarService;


class BundCarsApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $car_id = \Yii::$app->request->post('car_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $car_name = \Yii::$app->request->post('car_name');
        $service = BindCarsService::instance();
        $data = $service->bindCars($user_id, $car_id, $is_cooperation, $car_name);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        } else if(isset($data['ret']) && $data['ret'] == 28){
        	return $data;
        }
        return ['msg' => $data];
        
    }
}