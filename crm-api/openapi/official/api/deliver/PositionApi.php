<?php
namespace official\api\deliver;

use Yii;
use app\foundation\Api;
use app\services\DeliveryService;


/*
 * 商户的定位，通过配送人员id
 *   
 *   */
class PositionApi extends Api
{
    public function run()
    {
        $user_id = \Yii::$app->user->id;
        $service = DeliveryService::instance();
        $ret = $service->coordinate($user_id);
        
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result'=>$ret['result'],
        ];
    }    
}