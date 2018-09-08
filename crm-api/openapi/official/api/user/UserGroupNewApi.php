<?php
/**
 * Created by 牛广华.
 * User: Administrator
 * Date: 2017/6/14 0008
 */
namespace official\api\user;

use app\foundation\Api;
use app\services\UserGroupService;


class UserGroupNewApi extends Api
{
    public function run()
    {
        $department = \Yii::$app->request->post('department');
        $service = UserGroupService::instance();
        $result = $service->getUserGroupNew($department);
        if($result === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}