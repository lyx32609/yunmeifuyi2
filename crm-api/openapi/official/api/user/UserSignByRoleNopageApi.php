<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignByRoleNoPageService;
/**
 * 查询员工签到记录
 */
class UserSignByRoleNoPageApi extends Api
{
	public function run()
	{
		/**
		 * $startTime 开始时间
		 * $endTime 结束时间
		 * $user 用户id
		 */
		$user = \Yii::$app->request->post('user');
		$timeType = \Yii::$app->request->post('timeType');

		$uid = \Yii::$app->request->post('userid');
		$service = UserSignByRoleNoPageService::instance();
		$result = $service->getSignByRole($user,$timeType,$uid);
		if($result===false)
		{
            return $this->logicError($service->error, $service->errors);
		}
		return ['msg' => $result];
	}
}



