<?php
namespace official\api\user;

use app\foundation\Api;
use app\models\ApiCode;
use app\models\AccessToken;
use official\models\User;
use app\services\AccountService;
use official\Identity;
use app\models\AuthItemNum;
use app\models\CompanyCategroy;

class LoginApi extends Api
{
    public function run()
    {
        $username = \Yii::$app->request->post('username');
        $password = \Yii::$app->request->post('password');
        
        $identity = Identity::findByUsername($username);
        $company_num = CompanyCategroy::find()->select('company_num')->where(['id'=>$identity->company_categroy_id])->all();
        $company_num = $company_num[0]['company_num'] ? $company_num[0]['company_num'] : '';

        if(!$identity || $identity->password !== $password)
        {
            return $this->error(ApiCode::LOGIN_FAILED);
        }
        if($identity->is_staff == '0' || $identity->dimission_time > 0){
            return [
                'ret' => 100,
                'msg' => '暂无登录权限',
            ];
        }
        $company_time = CompanyCategroy::findOne($identity->company_categroy_id);
        if($company_time->review != '2'){
            return [
                'ret' => 100,
                'msg' => '公司正在审核或审核未通过'
            ];
        }
        if($company_time->failure == '1' && ( time() - $company_time->createtime > 864000)){
            return [
                'ret' => 100,
                'msg' => '公司超出试用期限',
            ];
        }
//         if($company_time->failure == 1){
//             return [
//                 'ret' => 100,
//                 'msg' => '公司正在审核或审核未通过'
//             ];
//         }
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
        return [
            'username' => $username,
            'IsSuperAdmin' => $roleKeyNum[0],
            'staffNum' => $identity->id, 
            'iSMorning' => $isTime,
            'HeadIcon' => \Yii::$app->params['uploadUrl'].'/'.$identity->head,
            'rank' => $identity->rank,
            'qrCode' => 'http://api.xunmall.com/qr/make?text=http://m.xunmall.com/site/register?staff='.$identity->model->username,
            'Text' => '问候语', 
            'token' => $accessToken,
            'domain' => $identity->domainId,
            'company_categroy_id' => $identity->company_categroy_id,
            'is_cooperation' => $company_time->type,
            'fly' => $company_time->fly,
            'company_num'=>$company_num,
        ];
    }
}