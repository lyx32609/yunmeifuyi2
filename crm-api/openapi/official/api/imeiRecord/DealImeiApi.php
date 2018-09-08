<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 8:36
 */
namespace official\api\imeiRecord;

use app\foundation\Api;
use app\services\PhoneImeiService;

class DealImeiApi extends Api
{
    public function run()
    {
        $put_imei_id = \Yii::$app->request->post('put_imei_id');
        $user_id = \Yii::$app->request->post('user_id');

        $service = PhoneImeiService::instance();
        $result = $service->dealImei($user_id, $put_imei_id);

        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];




    }
}