<?php
namespace app\services;

use app\foundation\Service;
use app\foundation\Identity;
use app\models\User;
use app\services\PushMessageService;
class AccountService extends Service
{
    /**
     * 刷新access_token
     * @param Identity $identity
     * @return string 新的token
     */
    public function refreshAccessToken(Identity $identity)
    {
        $now = time();
        
        if(($now - $identity->refreshTime) < \Yii::$app->params['reflshTime']) // 上次生成时间未超过设置时间则则返回老的token
        {
            return $identity->accessToken;
        }
        
        return $this->accessToken($identity);
    }
    
    /**
     * 生成access_token
     * @param Identity $identity
     * @return string token
     */
    private function accessToken(Identity $identity)
    {
        $str = \Yii::$app->accessContext->appid . '&' . \Yii::$app->accessContext->appkey . '&' . $identity->id . time();
        return md5($str);
    }
    /**
     * 判断时间（早、中、晚）
     * @param 
     * @return string 
     */
    public function isTime()
    {
        $time = date('H');
        if ($time>=0 && $time<=12)
        {
            return '早上好!';
        }
        elseif ($time>12 && $time<=19)
        {
            return '下午好!';
        }
        else
        {
          return '晚上好!';
        }    
    }

    public function updateCid($username, $cid, $appid, $appkey, $masterSecret)
    {
        if(!$cid)
        {
            $this->setError('cid不能为空!');
            return false;
        }
        if(!$appid)
        {
            $this->setError('appid不能为空!');
            return false;
        }
        if(!$appkey)
        {
            $this->setError('appkey不能为空!');
            return false;
        }
        if(!$masterSecret)
        {
            $this->setError('masterSecret不能为空!');
            return false;
        }
        $push = new PushMessageService;
        $auth = json_decode($push->auth($appid, $appkey, $masterSecret), true);
        if(!$auth['auth_token']){
          return $auth; //鉴权失败
        }
        $selectCid = json_decode($push->selectCid($auth['auth_token'], $appid, $username), true);
        if($selectCid['result'] == 'ok'){ 
            if($selectCid['cid'] == $cid){
                return $cid; //如果别名绑定cid且cid与需要绑定cid相同
            } else{
                $up_bind = json_decode($push->checkBind($auth['auth_token'], $appid, $username) ,true);
                if($up_bind['result'] != 'ok'){
                    return $up_bind; //解绑失败
                }
            }
        }
        $bind = json_decode($push->bind($auth['auth_token'], $appid, $cid, $username), true);
        if($bind['result'] != 'ok'){
          return $bind; //绑定失败
        }
        $user = User::find()->where(['username'=>$username])->one(); //获取username等于$username的模型
        $user->cid = $cid; //修改cid属性值
        if(!$user->save())
        {
            return $cid = 4; //帐号绑定Cid失败
        }
        
        return $cid;
    }
}
