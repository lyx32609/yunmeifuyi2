<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryNewService;



/* 
 * 存储订单发货记录信息
 *  
 *  */
class RecordNewApi extends Api {
    public function run() {
       	$user_id = \Yii::$app->request->post('user_id');
       	$is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $order_id=Yii::$app->request->post('order_id');
        $car_id = \Yii::$app->request->post('car_id');
        $car_name = \Yii::$app->request->post('car_name');
        $order_money = \Yii::$app->request->post('order_money');
        $order_pay = \Yii::$app->request->post('order_pay');
        $member_name = \Yii::$app->request->post('member_name');
        $service = DeliveryNewService::instance();
        $ret = $service->delivery_recordNew($order_id, $user_id, $is_cooperation, $company_category_id, $car_id, $car_name, $order_money, $order_pay, $member_name);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        return [
            'result' => $ret,
        ];
    }
}