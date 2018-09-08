<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 10:31
 */
namespace official\api\imeiRecord;

use app\foundation\Api;
use app\services\PhoneImeiService;

class RedDotApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');

        $service = PhoneImeiService::instance();
        $result = $service->redDot($user_id);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];




    }
}