<?php

namespace official\api\imeiRecord;

use app\foundation\Api;
use app\services\PhoneImeiService;

/*
 * 绑定手机串号
 * */
class BatchImeiApi extends Api
{
    public function run()
    {
        $imei_number = \Yii::$app->request->post('phone_imei');
        $user_id = \Yii::$app->request->post('user_id');
        $phone_brand= \Yii::$app->request->post('phone_brand');

        $service = PhoneImeiService::instance();
        $result = $service->batchImei($user_id, $imei_number,$phone_brand);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg'=>$result];


    }
}