<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\UserRealLocationService;

/**
 * 获取员工实时定位接口
 * @return array
 * @author lzk
 */
class GetRealLocationApi extends Api
{
    public function run()
    {
        $date = \Yii::$app->request->post('date');
        $user_id=\Yii::$app->request->post('user_id');
        $service = UserRealLocationService::instance();
        $data = $service->getRealLocation($user_id,$date);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return ['msg'=>$data];
    }
}