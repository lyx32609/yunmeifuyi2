<?php

namespace official\api\orders;

use app\foundation\Api;
use app\services\GetGoodsListService;

/**
 * 根据条形码和采购商,获取商品信息
 */
class GetGoodsListNewApi extends Api
{
    public function run()
    {
        $barCord = \Yii::$app->request->post('barCord');
        $memberId = \Yii::$app->request->post('memberId');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_categroy_id = \Yii::$app->request->post('company_category_id');
        $service = GetGoodsListService::instance();
        $result = $service->getGoodsBarCordNew($barCord, $memberId, $is_cooperation, $company_categroy_id);
        if($result === false) {
            	
            return $this->logicError($service->error, $service->errors);
        }

        return ['msg' => $result];
    }
}


