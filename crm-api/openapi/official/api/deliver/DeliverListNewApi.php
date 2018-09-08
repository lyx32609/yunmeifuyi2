<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryNewService;

/**
 * 发货列表接口
 * @return array 
 * @author lzk
 */
class DeliverListNewApi extends Api
{
    public function run()
    {
        $company_id = \Yii::$app->request->post('company_id');
        $page = \Yii::$app->request->post('page');
        $pageCount = \Yii::$app->request->post('pageCount');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $user_id = \Yii::$app->request->post('user_id');
        $service = DeliveryNewService::instance();
        $data = $service->getList($company_id, $page, $pageCount, $is_cooperation, $user_id);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $data;
    }
} 