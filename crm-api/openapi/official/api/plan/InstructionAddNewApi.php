<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\InstructionAddNewService;
class InstructionAddNewApi extends Api
{
	public function run()
	{

		$problem_id = \Yii::$app->request->post('problem_id');
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$instruction_content = \Yii::$app->request->post('instruction_content');
		$collaboration_department = \Yii::$app->request->post('collaboration_department');
		$appid = \Yii::$app->request->post('gtid');
        $appkey = \Yii::$app->request->post('gtkey');
        $masterSecret = \Yii::$app->request->post('gtmaster');

		$service = InstructionAddNewService::instance();
		
		$result = $service->instructionAdd($problem_id, $user_id, $user_name, $instruction_content, $collaboration_department, $appkey, $appid, $masterSecret);

		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];

	}
}