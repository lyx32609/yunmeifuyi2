<?php
namespace official\api\plan;

use app\foundation\Api;
use app\services\QuestionAddService;

class QuestionAddApi extends Api
{
	public function run()
	{
		$problem_id = \Yii::$app->request->post('problem_id');
		$user_id = \Yii::$app->request->post('user_id');
		$user_name = \Yii::$app->request->post('user_name');
		$question_content = \Yii::$app->request->post('question_content');

		$service = QuestionAddService::instance();
		$result = $service->questionAdd($problem_id, $user_id, $user_name, $question_content);

		if($result === false)
        {
            return $this->logicError($service->error);
        }
		return ['msg' => $result];

	}
}