<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignStateService;

    /**
     *  获取所选员工的签到记录
     * @parm string  userid 用户id的字符串 用逗号间隔
     * @return array
     * @author qizhifei
     */
class GetSelectedUserSignApi extends Api
{
    public function run()
    {
		$userid = \Yii::$app->request->post('userid');
		$page = \Yii::$app->request->post('page');
		$service = UserSignStateService::instance();
		$result = $service->getSelectedUserSign($userid, $page);
		if($result === false)
		{
            return $this->logicError($service->error, $service->errors);
		}
		return ['msg' => $result];
    }
}