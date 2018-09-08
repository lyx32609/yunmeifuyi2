<?php
namespace official\api\user;

use app\foundation\Api;
use official\models\User;
use app\services\UserImageService;

class UserImageApi extends Api
{
    public function run()
    {
        $imagePath = \Yii::$app->request->post('imagePath');
        if(!$imagePath)
        {
            return $this->logicError('地址不存在!');
        }
        
        $service = UserImageService::instance();
        $res = $service->saveImage($imagePath);
        if($res === false)
        {
            return $this->logicError($service->error);
        }
        
        return ['res'=>'保存成功'];
    }
}