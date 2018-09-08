<?php
namespace official\api\user;

use app\foundation\Api;
use official\models\User;
use app\services\UserService;
/**
 * 获取当前部门所有员工
 * @parm userid  传入的用户id
 * @return msg：array  当前部门所有员工",
 * @author qzf
 */
class GetDepartmentStaffApi extends Api
{
	public function run()
	{
		/**
		 * $userid 用户id
		 */
		$userid = \Yii::$app->request->post('userid');
		$service = UserService::instance();
		$result = $service->getDepartmentStaff($userid);
		if($result === false)
		{
            return $this->logicError($service->error, $service->errors);
		}
		return ['msg' => $result];
	}
}



