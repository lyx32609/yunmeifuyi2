<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryService;

/**
 * 签收列表接口
 * @return array 
 * @author lzk
 */
class DeliverSignListApi extends Api
{
    public function run()
    {
        $type = \Yii::$app->request->post('type');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');
        $page = \Yii::$app->request->post('page');
        $pageCount = \Yii::$app->request->post('pageCount');
        $service = DeliveryService::instance();
        $data = $service->deliverSignList($type,$start_time,$end_time,$page,$pageCount);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $data;
    }
}