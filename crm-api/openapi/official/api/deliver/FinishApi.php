<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\DeliveryService;


class FinishApi extends Api
{
    public function run()
    {
        $user_id=\Yii::$app->user->id;
        $flag=\Yii::$app->request->post('flag');
        $flag=$flag ? $flag:0;
        $service=DeliveryService::instance();
        $ret=$service->finish($user_id,$flag);
        if($ret===false)
        {
            return $this->logicError($service->error);
        }
        if(isset($ret['ret'])&&$ret['ret']==10)
        {
            return $ret;
        }
        return [
            'result'=>$ret,
        ];
    }
}