<?php


namespace official\api\imeiRecord;


use app\foundation\Api;
use app\services\ImeiService;

class UpdatePhoneImeiApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $imei = \Yii::$app->request->post('imei');
        $brand = \Yii::$app->request->post('brand');
        $company_categroy_id = \Yii::$app->request->post('company_categroy_id');
        $service = ImeiService::instance();
        $result = $service->updateNewImei($imei,$brand,$user_id,$company_categroy_id);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg' => $result];

    }

}