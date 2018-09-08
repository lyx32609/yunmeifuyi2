<?php
namespace official\api\user;

use Yii;
use app\foundation\Api;
use app\services\UserGroupMessageService;


/**
 * 根据区域  部门 获取先关工作人员 接口
 * @return array
 * @author 
 */
class GetDomainStaffApi extends Api
{
    public function run()
    {
        $domain_id = Yii::$app->request->post('domain_id');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');
        $service = UserGroupMessageService::instance();
        $result=$service->getDomainStaff($domain_id,$limit,$offset);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }

}