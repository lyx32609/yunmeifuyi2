<?php
namespace official\api\user;

use app\foundation\Api;
use app\models\ApiCode;
use official\models\User;
use official\Identity;

class UserValidApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        
        $identity = Identity::findByUsername($username);
        if(!$identity)
        {
            return $this->error(ApiCode::USER_IS_EXIST);
        }
        else
        {
            return [
                'msg' => '存在', 
            ];
        }
    }
}