<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\WmsNewService;

class DeliveryNoteNewApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $company_category_id = \Yii::$app->request->post('company_category_id');
        $car_id = \Yii::$app->request->post('car_id');
        $car_name = \Yii::$app->request->post('car_name');
        $service = WmsNewService::instance();
        $ret = $service->deliveryNoteNew($user_id, $is_cooperation, $company_category_id, $car_id, $car_name);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result'=>$ret,
        ];
    }
}