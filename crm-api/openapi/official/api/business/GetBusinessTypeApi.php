<?php
namespace official\api\business;
use app\foundation\Api;
use app\services\GetBusinessTypeService;
/**
 * Created by 付腊梅.
 * User: Administrator
 * Date: 2017/4/20 
 * Time: 下午 17:00
 */


class GetBusinessTypeApi extends Api
{
    public function run()
    {

        $service = GetBusinessTypeService::instance();
        $result =$service->getBusinessType();
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
    }
}