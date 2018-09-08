<?php
namespace official\api\hr;

use app\foundation\Api;
use Yii;
use app\services\GetCompanyDepartmentUserListService;

class GetCompanyDepartmentUserListApi extends Api
{
    public function run()
    {
        $department_id = Yii::$app->request->post('department_id');    
        $company_categroy_id = Yii::$app->request->post('company_categroy_id');
        $service = GetCompanyDepartmentUserListService::instance();
        $result = $service->getCompanyDepartmentUserLis($department_id, $company_categroy_id);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];
    }
}