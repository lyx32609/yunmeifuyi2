<?php
namespace official\api\submen;

use app\foundation\Api;
use app\services\GetSubmenuTwoService;


class GetSubmenuTwoApi extends Api
{
    public function run()
    {
        $user_company_id = \Yii::$app->request->post('user_company_id');
        $area = \Yii::$app->request->post('area');
        $city = \Yii::$app->request->post('city');
        $department_name = \Yii::$app->request->post('department_name');
        $department_id = \Yii::$app->request->post('department_id');
        $company_name = \Yii::$app->request->post('company_name');
        $company_id = \Yii::$app->request->post('company_id');
        $timeType = \Yii::$app->request->post('timeType');
        $service = GetSubmenuTwoService::instance();
        $result =$service->getSubmenu($user_company_id,$area,$city,$department_name,$department_id,$company_name,$company_id,$timeType);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return $result;
        
    }
}