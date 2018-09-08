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
class GetDepartmentUserListApi extends Api
{
    public function run()
    {
        $department_id = Yii::$app->request->post('department_id');
        $service = UserGroupMessageService::instance();
        $result=$service->getDepartmentUserList($department_id);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }

}