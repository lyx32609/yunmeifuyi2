<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryService;



/* 
 * 存储订单发货记录信息
 *  
 *  */
class RecordApi extends Api {
    function run() {
        $user_id=Yii::$app->user->id;
      //  $car_num=Yii::$app->request->post('car_num');
        $order_id=Yii::$app->request->post('order_id');
        $service=DeliveryService::instance();
        $ret=$service->delivery_record($order_id,$user_id);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        return [
            'result' => $ret,
        ];
    }
}