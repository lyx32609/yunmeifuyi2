<?php

namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryNewService;

class BatchNewApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->user->id;
        $car_id = \Yii::$app->request->post('car_id');
        $car_name = \Yii::$app->request->post('car_name');
        $car_driver_name = \Yii::$app->request->post('car_driver_name');
        $car_driver_phone = \Yii::$app->request->post('car_driver_phone');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = DeliveryNewService::instance();
        $ret = $service->batch($user_id, $car_id, $car_name, $car_driver_name, $car_driver_phone, $is_cooperation, $company_category_id);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret']) && $ret['ret'] == 2)
        {
            return $ret;
        }
        return [
            'result' => $ret,
        ];
    }

}