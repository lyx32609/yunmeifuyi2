<?php

namespace official\api\hr;

use app\foundation\Api;
use app\services\SaveWorkTimeService;
class SaveWorkTimeApi extends Api
{
    public function run()
    {
        $company_categroy_id = \Yii::$app->request->post('company_categroy_id');
        $start = \Yii::$app->request->post('start');
        $end = \Yii::$app->request->post('end');
        $type = \Yii::$app->request->post('type');
        $service = SaveWorkTimeService::instance();
        $res = $service->saveWorkTime($company_categroy_id, $start, $end, $type);
        if($res === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $res];
    }
}