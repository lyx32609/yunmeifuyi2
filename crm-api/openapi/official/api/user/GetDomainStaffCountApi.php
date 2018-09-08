<?php
namespace official\api\user;

use Yii;
use app\foundation\Api;
use app\services\UserGroupMessageService;


/**
 * 根据区域  部门 获取先关工作人员总数 接口
 * @return array
 * @author 
 */
class GetDomainStaffCountApi extends Api
{
    public function run()
    {
        $domain_id = Yii::$app->request->post('domain_id');
        $service = UserGroupMessageService::instance();
        $result = $service->getDomainStaffCount($domain_id);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => count($result)];
    }

}