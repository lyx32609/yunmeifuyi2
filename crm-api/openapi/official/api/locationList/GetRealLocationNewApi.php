<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\UserRealLocationNewService;

class GetRealLocationNewApi extends Api
{
    public function run()
    {
        $date = \Yii::$app->request->post('date');
        $user_id=\Yii::$app->request->post('user_id');
        $service = UserRealLocationNewService::instance();
        $data = $service->getRealLocation($user_id,$date);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$data];
    }
}