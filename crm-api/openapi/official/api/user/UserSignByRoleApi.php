<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignByRoleService;
/**
 * 查询员工签到记录
 */
class UserSignByRoleApi extends Api
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
		$uid = \Yii::$app->request->post('userid');
		$page = \Yii::$app->request->post('page');
		$service = UserSignByRoleService::instance();
		$result = $service->getSignByRole($user, $startTime, $endTime, $uid, $page);
		if($result===false)
		{
            return $this->logicError($service->error, $service->errors);
		}
		return ['msg' => $result];
	}
}



