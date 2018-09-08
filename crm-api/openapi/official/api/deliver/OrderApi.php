<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryService;

/*
 * 获取商户 订单 订单详情 综合信息
 *   
 *   
 *   */
class OrderApi extends Api
{
    public function run()
    {
        $order_id=Yii::$app->request->post('order_id');
        $service= DeliveryService::instance();
        $ret=$service->order($order_id);
      
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result'=>$ret,
        ];
    }
}