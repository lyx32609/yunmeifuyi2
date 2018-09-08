<?php
namespace official\api\user;

use app\foundation\Api;
use Yii;
use app\services\UserGroupMessageService;


/**
 * 根据区域  部门 列表 接口
 * @return array
 * @author
 */
class GetDomainDepartmentListApi extends Api
{
    public function run()
    {
        $domain_id = Yii::$app->request->post('domain_id');
        $service = UserGroupMessageService::instance();
        $result=$service->getDomainDepartmentList($domain_id);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }

}