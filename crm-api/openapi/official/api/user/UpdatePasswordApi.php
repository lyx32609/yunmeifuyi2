<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserService;

class UpdatePasswordApi extends Api
{
    public function run()
    {
        $password = \Yii::$app->request->post('password');
        
        $service = UserService::instance();
        $res = $service->updatePassword($password);
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return [];
    }
        
}