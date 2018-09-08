<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignService;
/**
 * 查询员工签到记录
 */
class HistorySignApi extends Api
{
	public function run()
	{
		/**
		 * $startTime 开始时间
		 * $endTime 结束时间
		 * $user 用户id
		 */
		$user = \Yii::$app->request->post('user');
		$startTime = \Yii::$app->request->post('startTime');
		$endTime = \Yii::$app->request->post('endTime');
		$service = UserSignService::instance();
		$result = $service->getHistroySign($user, $startTime, $endTime);
		if($result===false)
		{
            return $this->logicError($service->error, $service->errors);
		}
		return ['msg' => $result];
	}
}



