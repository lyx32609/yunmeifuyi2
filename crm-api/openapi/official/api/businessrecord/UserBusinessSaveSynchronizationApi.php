<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserBusinessService;

/**
 * 同步数据
 * @return msg："保存成功",result ：""
 * @author 
 */
class UserBusinessSaveSynchronizationApi extends Api
{
    public function run()
    {
        $service = UserBusinessService::instance();
        $res = $service->Synchronization();
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}