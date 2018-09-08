<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\SaveWorkTimeService;
class SaveWorkTimeNewApi extends Api
{
    public function run()
    {
        $status = \Yii::$app->request->post('status');
        $morning_to_work = \Yii::$app->request->post('morning_to_work');
        $morning_go_work = \Yii::$app->request->post('morning_go_work');
        $company_id = \Yii::$app->request->post('company_id');
        $uid = \Yii::$app->request->post('user_id');
        $after_to_work = \Yii::$app->request->post('after_to_work');
        $after_go_work = \Yii::$app->request->post('after_go_work');
        $service = SaveWorkTimeService::instance();
        $res = $service->saveWorkTimeNew($status, $morning_to_work, $morning_go_work, $company_id, $uid, $after_to_work, $after_go_work);
        if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $res];
    }
}