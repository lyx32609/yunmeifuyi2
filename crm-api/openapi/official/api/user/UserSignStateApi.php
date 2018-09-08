<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignStateService;

/**
 * 外勤签到、签退查询
 * @return msg："未签到"？"未签退",
 * @author lzk
 */
class UserSignStateApi extends Api
{
    public function run()
    {
        $user = \Yii::$app->user->id;
        $service = UserSignStateService::instance();
        $res = $service->getUserSignState($user);
        if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $res;
    }
}