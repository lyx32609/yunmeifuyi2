<?php

namespace official\api\imeiRecord;


use app\foundation\Api;
use app\services\ImeiService;

class ImeiDetailsApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $start_time = \Yii::$app->request->post('start_time');
        $end_time = \Yii::$app->request->post('end_time');
        $service = ImeiService::instance();
        $result = $service->imeiDetails($user_id,$start_time,$end_time);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];

    }
}