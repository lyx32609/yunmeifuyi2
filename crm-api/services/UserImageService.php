<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;

class UserImageService extends Service
{
    
    public function saveImage($imagePath)
    {
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        $user->head = $imagePath;
        if(!$user->save())
        {
            $this->setError('头像修改失败', $user->errors);
            return false;
        }
        
        return true;
    }
    
    
}