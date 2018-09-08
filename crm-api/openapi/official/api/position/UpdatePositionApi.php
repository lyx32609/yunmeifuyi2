<?php
namespace official\api\position;

use app\foundation\Api;
use app\services\PositionService;

class UpdatePositionApi extends Api
{
    public function run()
    {
        $identity=\Yii::$app->request->post('identity');
        $id = \Yii::$app->request->post('shopid');
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        
        $service = PositionService::instance();
       
        $res = $service->update($identity,$id, $longitude,$latitude);
        
        if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        
        return [];
    }
}