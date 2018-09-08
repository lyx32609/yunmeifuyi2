<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\foundation\Service;
use app\services\UserBusinessService;

/**
 * 根据客户名称模糊查询相关业务信息
 * @return array 
 * @author lzk
 */
class GetBusinessApi extends Api
{
    public function run()
    {
        $businessName = \Yii::$app->request->post('businessName');
        $type = \Yii::$app->request->post('type');
        $page = \Yii::$app->request->post('page');
        $pageSize = \Yii::$app->request->post('pageSize');
        $service = UserBusinessService::instance();
        $data = $service->getBusinessName($businessName,$type,$page,$pageSize);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}