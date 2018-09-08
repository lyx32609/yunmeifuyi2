<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryService;

/**
 * 发货列表接口
 * @return array 
 * @author lzk
 */
class DeliverListApi extends Api
{
    public function run()
    {
        $page = \Yii::$app->request->post('page');
        $pageCount = \Yii::$app->request->post('pageCount');
        $service = DeliveryService::instance();
        $data = $service->deliverList($page,$pageCount);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $data;
    }
}