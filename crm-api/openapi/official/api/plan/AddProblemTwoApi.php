<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\GetBusinessProblemInfoService;
class AddProblemTwoApi extends Api
{
	/**
	 * 提交问题接口
	 * 
	 */
	public function run()
	{
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$problem_title = \Yii::$app->request->post('problem_title');
		$problem_content = \Yii::$app->request->post('problem_content');
		$collaboration_department = \Yii::$app->request->post('collaboration_department');
		$priority = \Yii::$app->request->post('priority');
		
		$service = GetBusinessProblemInfoService::instance();
		$result = $service->addProblem($user_id, $user_name,$problem_title, $problem_content, $priority, $collaboration_department);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return $result;
	}
}