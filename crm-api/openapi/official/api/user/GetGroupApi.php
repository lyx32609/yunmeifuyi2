<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserGroupMessageService;

/**
 * 根据区域   获取该区域分组查询 接口
 * @return array 
 * @author 
 */
class GetGroupApi extends Api
{
    public function run()
    {
        $domain_id=\Yii::$app->request->post('domain_id');
        $service = UserGroupMessageService::instance();
        $result = $service->getGroups($domain_id);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $result;
    }
}