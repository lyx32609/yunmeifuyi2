<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryNewService;

/**
 * 发货签收接口
 * @return array 
 * @author lzk
 */
class DeliverPaySignNewApi extends Api
{
    public function run()
    {
        $order_id = \Yii::$app->request->post('order_id');
        $payMent = \Yii::$app->request->post('payMent');
        $is_paySign = \Yii::$app->request->post('is_paySign');
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = DeliveryNewService::instance();
        $data = $service->updatePaysign($order_id, $payMent, $is_paySign, $user_id, $is_cooperation, $company_category_id);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return[];
    }
}