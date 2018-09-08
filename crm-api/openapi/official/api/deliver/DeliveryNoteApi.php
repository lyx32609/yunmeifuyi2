<?php
namespace official\api\deliver;

use app\foundation\Api;
use app\services\WmsService;

class DeliveryNoteApi extends Api
{
    public function run()
    {
        $user_id=\Yii::$app->user->id;
        $service=WmsService::instance();
        $ret=$service->deliveryNote($user_id);
        if($ret === false)
        {
            return $this->logicError($service->error);
        }
        
        return [
            'result'=>$ret,
        ];
    }
}