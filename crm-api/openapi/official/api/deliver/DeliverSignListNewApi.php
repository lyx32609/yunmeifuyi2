<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryNewService;

/**
 * 签收列表接口
 * @return array 
 * @author lzk
 */
class DeliverSignListNewApi extends Api
{
    public function run()
    {
        $company_id = \Yii::$app->request->post('company_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $type = \Yii::$app->request->post('type');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');
        $page = \Yii::$app->request->post('page');
        $pageCount = \Yii::$app->request->post('pageCount');
        $service = DeliveryNewService::instance();
        $ret = $service->getSignList($company_id, $type, $start_time, $end_time, $page, $pageCount, $is_cooperation);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        return $ret;
    }
}