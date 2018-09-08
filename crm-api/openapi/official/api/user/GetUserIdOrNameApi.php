<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\GetUserIdOrNameService;

    /**
     *  获取条件下的用户名或用户ID
     * @return array
     * @author 付腊梅
     */
class GetUserIdOrNameApi extends Api
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
        $type = \Yii::$app->request->post('type');
		$service = GetUserIdOrNameService::instance();
		$result = $service->getuserDataByType($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $type);
		if($result == false)
		{
            return $this->logicError($service->error, $service->errors);
		}
        else
        {
            return ['msg' => $result];
        }
        //return ['msg' => $user_company_id];
		
    }
}