<?php
namespace official\api\user;

use app\foundation\Api;
use app\models\ApiCode;
use app\models\AccessToken;
use official\models\User;
use app\services\AccountService;
use official\Identity;
use app\models\AuthItemNum;

class UpdateUserCidApi extends Api
{
    public function run() //暂时弃用
    {
        $username = \Yii::$app->request->post('username');
        $password = \Yii::$app->request->post('password');
        $cid = \Yii::$app->request->post('cid');
        $appid = \Yii::$app->request->post('gtid');
        $appkey = \Yii::$app->request->post('gtkey');
        $masterSecret = \Yii::$app->request->post('gtmaster');
        
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
        if(!$roleKeyNum){
            return [
                'ret' => 100,
                'msg' => '暂无登录权限',
            ];
        }
        $service =  AccountService::instance();
        $result = $service->updateCid($username,$cid,$appid, $appkey, $masterSecret);
       //(int)\Yii::$app->authManager->checkAccess($identity->id, 'admin')
       if($result === false){
            return $this->logicError($service->error);
       }
        return [
            'username' => $username,
            'IsSuperAdmin' => $roleKeyNum[0],
            'staffNum' => $identity->id, 
            'iSMorning' => $isTime,
            'HeadIcon' => \Yii::$app->params['uploadUrl'].'/'.$identity->head,
            'rank' => $identity->rank,
            'qrCode' => 'http://api.xunmall.com/qr/make?text=http://m.xunmall.com/site/register?staff='.$identity->model->username,
            'Text' => '问候语', 
            //'token' => AccountService::instance()->refreshAccessToken($identity),
            'token' => $accessToken,
            'cid' =>$result,
        ];
    }
}