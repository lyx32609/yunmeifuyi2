<?php
namespace official\api\VisitShop;

use app\foundation\Api;
use app\services\VisitShopService;

/**
 * 指定日期内的到访店铺集合
 * @return array 
 * @author lzk
 */
class VisitShopApi extends Api
{
    public function run()
    {
        $staffId = \Yii::$app->request->post('staffId');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $service = VisitShopService::instance();
        $data = $service->getVisitShop($staffId,$start,$end);
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['shopList'=>$data];
    }
}