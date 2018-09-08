<?php
namespace official\api\user;

use app\foundation\Api;
use app\models\ApiCode;
use app\models\AccessToken;
use official\models\User;
use app\services\AccountService;
use official\Identity;
use app\models\AuthItemNum;

class LoginNewApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $password = \Yii::$app->request->post('password');
        
        $identity = Identity::findByUsername($username);
        
        if(!$identity || $identity->password !== $password)
        {
            return $this->error(ApiCode::LOGIN_FAILED);
        }
        
        $accessToken = AccountService::instance()->refreshAccessToken($identity);
        $identity->updateAccessToken($accessToken);
        $isTime = AccountService::instance()->isTime();
        $roleNames = \Yii::$app->authManager->getRolesByUser($identity->id);
        $roleKeyName = key($roleNames);
        $roleKeyNum = AuthItemNum::find()->select('item_num')
                      ->where('item_name = :roleKeyName',[':roleKeyName'=>$roleKeyName])
                      ->column();
       //(int)\Yii::$app->authManager->checkAccess($identity->id, 'admin')
        return [
            'username' => $username,
            'IsSuperAdmin' => $roleKeyNum[0],
            'staffNum' => $identity->id, 
            'iSMorning' => $isTime,
            'HeadIcon' => \Yii::$app->params['uploadUrl'].'/'.$identity->head,
            'rank' => $identity->rank,
            'qrCode' => 'http://api.xunmall.com/qr/make?text=http://m.xunmall.com/site/register?staff='.$identity->model->username,
            'Text' => '问候语', 
            'token' => AccountService::instance()->refreshAccessToken($identity),
        ];
    }
}