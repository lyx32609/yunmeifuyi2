<?php
namespace official\api\shop;

use app\foundation\Api;
use app\services\GetShopsNewInfoService;


class GetShopsNewInfoApi extends Api
{
    public function run()
    {
        $shopName = \Yii::$app->request->post('shopName');
        $type = \Yii::$app->request->post('type');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $join = \Yii::$app->request->post('join');
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $service = GetShopsNewInfoService::instance();
        $data = $service->getShopsName($shopName,$type,$page,$pageSize,$join,$user_id, $is_cooperation, $company_category_id);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        } else if (isset($data['ret']) && $data['ret']){
            return $data;
        }
        return ['result' => $data];
    }
}