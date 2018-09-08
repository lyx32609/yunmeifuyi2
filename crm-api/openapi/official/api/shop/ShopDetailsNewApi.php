<?php
namespace official\api\shop;

use app\foundation\Api;
use app\services\ShopNewService;

/**
 * 获取店铺详情
 * @return array ['Shopid'=>店铺id, 'shopName'=>店铺名称, 'longitude'=>经度, 'Latitude'=>纬度, 'orderamount'=>订单数量 , 'orderPrice'=>订单金额, 'OrderLatelyDate'=>最后下单时间]
 * @author lzk
 */
class ShopDetailsNewApi extends Api
{
    public function run()
    {
        $shop_id = \Yii::$app->request->post('shop_id');
        $type = \Yii::$app->request->post('type');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $service = ShopNewService::instance();
        $data = $service->getShopDetails($shop_id, $type, $is_cooperation);
        if(!$data)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}