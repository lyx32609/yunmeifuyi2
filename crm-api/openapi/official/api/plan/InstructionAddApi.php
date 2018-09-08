<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\InstructionAddService;
class InstructionAddApi extends Api
{
	public function run()
	{

		$problem_id = \Yii::$app->request->post('problem_id');
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$instruction_content = \Yii::$app->request->post('instruction_content');
		$collaboration_department = \Yii::$app->request->post('collaboration_department');

		$service = InstructionAddService::instance();
		
		$result = $service->instructionAdd($problem_id, $user_id, $user_name, $instruction_content, $collaboration_department);

		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];

	}
}