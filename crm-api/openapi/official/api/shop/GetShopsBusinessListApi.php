<?php
namespace official\api\shop;

use app\foundation\Api;
use app\foundation\Service;
use app\services\GetShopsNewInfoService;

/**
 * 根据名称模糊查询本地相关店铺/供应商（改版后）
 * @return array 
 * @author 
 */
class GetShopsBusinessListApi extends Api
{
    public function run()
    {
        $shopName = \Yii::$app->request->post('shopName');
        $domain_id = \Yii::$app->request->post('domain_id');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $type =\Yii::$app->request->post('type');
        $status = \Yii::$app->request->post('status');
        $service = GetShopsNewInfoService::instance();
        $data = $service->getBusinessList($shopName, $domain_id, $page, $type, $pageSize, $status);
        if($data === false)
        {
            return [
               'ret' => 100,
                'msg' => '暂无数据'
            ];
        }
        return ['result'=>$data];
    }
}