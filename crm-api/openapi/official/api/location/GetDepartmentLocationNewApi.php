<?php
namespace official\api\location;

use app\foundation\Api;
use app\services\GetDepartmentLocationNewService;


class GetDepartmentLocationNewApi extends Api
{
    public function run()
    {
        $department_id = \Yii::$app->request->post('department_id');
        $user_id = \Yii::$app->request->post('user_id');
        $type = \Yii::$app->request->post('type');
        $service = GetDepartmentLocationNewService::instance();
        $result = $service->getDepartmentLocation($department_id, $user_id, $type);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}