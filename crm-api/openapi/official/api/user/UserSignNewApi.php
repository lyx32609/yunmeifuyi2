<?php
namespace official\api\user;

use app\foundation\Api;
use app\services\UserSignNewService;


class UserSignNewApi extends Api
{
    public function run()
    {
        $user = \Yii::$app->user->id;
        $type = \Yii::$app->request->post('type');
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $image = \Yii::$app->request->post('image');
        $path = \Yii::$app->request->post('path');
        $service = UserSignNewService::instance();
        $result = $service->add($user, $type, $longitude, $latitude, $image, $path);
        if($result===false)
        {
            return $this->logicError($service->error, $service->errors);
        }
        return $result;
    }
}