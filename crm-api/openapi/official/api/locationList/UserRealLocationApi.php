<?php
namespace official\api\LocationList;

use app\foundation\Api;
use app\services\UserRealLocationNewService;

/**
 * 员工实时定位接口
 * @return array
 * @author lzk
 */
class UserRealLocationApi extends Api
{
    public function run()
    {
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $user_id=\Yii::$app->user->id;
        $service = UserRealLocationNewService::instance();
        $data = $service->userRealLocation($user_id,$longitude,$latitude);
        if($data === false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $data;
    }
}