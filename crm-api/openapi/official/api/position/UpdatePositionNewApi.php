<?php
namespace official\api\position;

use app\foundation\Api;
use app\services\PositionNewService;

class UpdatePositionNewApi extends Api
{
    public function run()
    {
        $identity=\Yii::$app->request->post('identity');
        $id = \Yii::$app->request->post('shopid');
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        
        
        $service = PositionNewService::instance();
       
        $res = $service->update($identity,$id, $longitude,$latitude, $is_cooperation);
        
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        
        return [];
    }
}