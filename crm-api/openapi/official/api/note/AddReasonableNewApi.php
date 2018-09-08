<?php
namespace official\api\note;

use app\foundation\Api;
use app\services\NoteNewService;
use app\models\User;

class AddReasonableNewApi extends Api
{
    public function run()
    {
        $shopId = \Yii::$app->request->post('shopid');
        $conte = \Yii::$app->request->post('conte');
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $longitude = \Yii::$app->request->post('longitude');
        $latitude = \Yii::$app->request->post('latitude');
        $belong = \Yii::$app->request->post('belong');
        $imag = \Yii::$app->request->post('imag');
        $is_cooperation = \Yii::$app->request->post('is_cooperation');
        $service = NoteNewService::instance();
        $res = $service->addReasonable($shopId, $conte, $user['username'], $longitude, $latitude, $imag, $belong, $is_cooperation);
         if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        
        return [];
    }
}

?>