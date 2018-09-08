<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryNewService;


class FinishNewApi extends Api
{
    public function run()
    {
       	$user_id = \Yii::$app->request->post('user_id');
        $flag = \Yii::$app->request->post('flag');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $car_id = \Yii::$app->request->post('car_id'); 
        $car_name = \Yii::$app->request->post('car_name');
        $flag = $flag ? $flag : 0;
        $service = DeliveryNewService::instance();
        $ret = $service->finishNew($user_id, $flag, $is_cooperation, $company_category_id, $car_id, $car_name);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret']) && $ret['ret'] == 10)
        {
            return $ret;
        }
        return [
            'result' => $ret,
        ];
    }
}