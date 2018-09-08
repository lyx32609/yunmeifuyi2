<?php
namespace official\api\shop;

use app\foundation\Api;
use app\foundation\Service;
use app\services\ShopService;

/**
 * 根据名称模糊查询相关店铺/供应商
 * @return array 
 * @author lzk
 */
class GetShopsNewApi extends Api
{
    public function run()
    {
        $shopName = \Yii::$app->request->post('shopName');
        $type = \Yii::$app->request->post('type');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $join=\Yii::$app->request->post('join');
        $user_id=\Yii::$app->request->post('user_id');
        $service = ShopService::instance();
        $data = $service->getShopsName($shopName,$type,$page,$pageSize,$join,$user_id);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}