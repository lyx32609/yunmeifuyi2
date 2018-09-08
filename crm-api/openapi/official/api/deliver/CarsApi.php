<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\WmsService;


class CarsApi extends Api
{
    public function run()
    {
        $user_id=\Yii::$app->user->id;
        $service=WmsService::instance();
        $ret=$service->getCars($user_id);
        if($ret===false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret'])&&$ret['ret']==10)
        {
            return $ret;
        }
        if(isset($ret['ret'])&&$ret['ret']==2)
        {
            return $ret;
        }
        if(isset($ret['ret'])&&$ret['ret']==28)
        {
            return $ret;
        }
        return [
            'result'=>$ret,
        ];
        
    }
}