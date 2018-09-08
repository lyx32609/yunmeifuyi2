<?php
namespace official\api\business;

use app\foundation\Api;
use app\services\GetBusinessDataThreeService;


class GetBusinessDataThreeApi extends Api
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
       	$num = \Yii::$app->request->post('num');
        $service = GetBusinessDataThreeService::instance();
        $result = $service->getData($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $num);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
        
    }
}