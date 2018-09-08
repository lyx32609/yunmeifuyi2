<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserIndexService;

/**
 * 计划任务  统计昨日的员工指令
 * @return msg：
 * @author qzf
 */
class UserIndexApi extends Api
{
    public function run()
    {
        $service = UserIndexService::instance();
        $res = $service->index();
        if($res===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}