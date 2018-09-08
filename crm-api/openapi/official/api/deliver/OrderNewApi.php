<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryNewService;

/*
 * 获取商户 订单 订单详情 综合信息(改版后)
 *   
 *   
 *   */
class OrderNewApi extends Api
{
    public function run()
    {
        $order_id = Yii::$app->request->post('order_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = DeliveryNewService::instance();
        $ret = $service->orderNew($order_id, $is_cooperation, $company_category_id);
      
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result' => $ret,
        ];
    }
}