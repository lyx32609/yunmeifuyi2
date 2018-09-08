<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryService;


/**
 * 发货签收接口
 * @return array 
 * @author lzk
 */
class DeliverPaySignApi extends Api
{
    public function run()
    {
        $order_id = \Yii::$app->request->post('order_id');
        $payMent = \Yii::$app->request->post('payMent');
        $is_paySign = \Yii::$app->request->post('is_paySign');
        $user_id = \Yii::$app->user->id;
        $service = DeliveryService::instance();
        $data = $service->updatePaysign($order_id,$payMent,$is_paySign,$user_id);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return[];
    }
}