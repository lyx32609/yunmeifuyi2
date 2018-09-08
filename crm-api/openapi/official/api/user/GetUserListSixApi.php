<?php
namespace official\api\user;

use app\foundation\Api;
use Yii;
use app\services\GetUserListSixService;

class GetUserListSixApi extends Api
{
    public function run()
    {
        $area_id = Yii::$app->request->post('area_id');
        $city_id = Yii::$app->request->post('city_id');
        $company_id = Yii::$app->request->post('company_id');
        $department_id = Yii::$app->request->post('department_id');    
        $service = GetUserListSixService::instance();
        $result = $service->getUserList($area_id, $city_id, $company_id, $department_id);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}