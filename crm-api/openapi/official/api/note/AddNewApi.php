<?php
namespace official\api\note;

use app\foundation\Api;
use app\services\NoteService;
use app\models\User;

class AddNewApi extends Api
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
        $service = NoteService::instance();
        $res = $service->add($shopId,$conte,$user['username'],$longitude,$latitude,$imag,$belong);
         if(!$res)
        {
            return $this->logicError($service->error, $service->errors);
        }
        
        return [];
    }
}

?>