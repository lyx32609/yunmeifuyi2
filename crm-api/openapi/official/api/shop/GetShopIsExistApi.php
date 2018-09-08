<?php
namespace official\api\shop;

use app\foundation\Api;
use app\foundation\Service;
use app\services\ShopService;

/**
 * 根据信息查询接口集采数据库是否存在数据
 * @return array 
 * @author qzf
 */
class GetShopIsExistApi extends Api
{
    public function run()
    {
        $mobile = \Yii::$app->request->post('mobile');
        $service = ShopService::instance();
        $result = $service->getIsShop($mobile);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
    }
}

