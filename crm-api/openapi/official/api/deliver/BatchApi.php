<?php

namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryService;

class BatchApi extends Api
{
    public function run()
    {
        $user_id=\Yii::$app->user->id;
        $car_id=\Yii::$app->request->post('car_id');
        $car_name=\Yii::$app->request->post('car_name');
        $car_driver_name=\Yii::$app->request->post('car_driver_name');
        $car_driver_phone=\Yii::$app->request->post('car_driver_phone');
        $service=DeliveryService::instance();
        $ret=$service->batch($user_id,$car_id,$car_name,$car_driver_name,$car_driver_phone);
        if($ret===false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret'])&&$ret['ret']==2)
        {
            return $ret;
        }
        return [
            'result'=>$ret,
        ];
        
    }

}