<?php
namespace official\api\shop;

use app\foundation\Api;
use app\foundation\Service;
use app\services\ShopNewService;

/**
 * 我的店铺
 * @return array 
 * @author lzk
 */
class ShopNewApi extends Api
{
    public function run()
    {

        $mobile = trim(\Yii::$app->request->post('mobile'));
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = ShopNewService::instance();
        $data = $service::instance()->getShop($mobile, $is_cooperation, $company_category_id);       
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}