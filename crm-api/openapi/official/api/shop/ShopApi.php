<?php
namespace official\api\shop;

use app\foundation\Api;
use app\foundation\Service;
use app\services\ShopService;

/**
 * 我的店铺
 * @return array 
 * @author lzk
 */
class ShopApi extends Api
{
    public function run()
    {

        $mobile = trim(\Yii::$app->request->post('mobile'));
        $service = ShopService::instance();
        $data = $service::instance()->getShop($mobile);       
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}