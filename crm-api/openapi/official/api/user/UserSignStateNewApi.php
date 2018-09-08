<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignStateService;

class UserSignStateNewApi extends Api
{
    public function run()
    {
        $user = \Yii::$app->user->id;
        $service = UserSignStateService::instance();
        $res = $service->getUserSignNew($user);
        if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $res];
    }
}