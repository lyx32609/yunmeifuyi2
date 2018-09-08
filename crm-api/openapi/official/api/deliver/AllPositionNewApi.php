<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryNewService;


/*
 * 获取批次店铺相关信息
 *   
 *   */
class AllPositionNewApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = DeliveryNewService::instance();
        $ret = $service->getAllPosition($user_id, $is_cooperation, $company_category_id);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        return [
            'result'=>$ret,
        ];
    }    
}