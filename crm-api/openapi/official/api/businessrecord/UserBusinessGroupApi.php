<?php
namespace official\api\businessrecord;

use app\foundation\Api;
use app\services\UserRecordNewService;

/**
 * 业务分组查询接口
 * @return array 
 * @author lzk
 */
class UserBusinessGroupApi extends Api
{
    public function run()
    {
        $service = UserRecordNewService::instance();
        $data = $service->getGroups();
        if($data===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['result'=>$data];
    }
}