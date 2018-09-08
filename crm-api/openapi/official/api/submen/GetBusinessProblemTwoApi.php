<?php
namespace official\api\submen;

use app\foundation\Api;
use app\services\GetBusinessProblemTwoService;


class GetBusinessProblemTwoApi extends Api
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
        $startTime = \Yii::$app->request->post('startTime');
        $endTime = \Yii::$app->request->post('endTime');
        $service = GetBusinessProblemTwoService::instance();
        $data = $service->getBusinessProblem($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $startTime, $endTime);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$data];
    }
}