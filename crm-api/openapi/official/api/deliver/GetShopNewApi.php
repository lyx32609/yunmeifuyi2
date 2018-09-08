<?php

namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\GetShopNewService;

class GetShopNewApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $shop_name = \Yii::$app->request->post('shop_name');
        $service = GetShopNewService::instance();
        $ret = $service->getShopNew($user_id, $is_cooperation, $company_category_id, $shop_name);
        
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result'=>$ret,
        ];
    }
}

