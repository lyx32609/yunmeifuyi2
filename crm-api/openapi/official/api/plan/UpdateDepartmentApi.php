<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\AddProblemService;

class UpdateDepartmentApi extends Api
{
	public function run()
	{
		$problem_id = \Yii::$app->request->post('problem_id');
		$collaboration_department = \Yii::$app->request->post('collaboration_department');
		$service = AddProblemService::instance();
		$result = $service->updateDepartment($problem_id, $collaboration_department);
		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];
	}
}