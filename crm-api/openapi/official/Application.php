<?php
namespace official;

use app\foundation\AccessContext;
use app\models\ApiCode;
use app\foundation\Api;
require_once ROOT.'/foundation/Application.php';

class Application extends \app\foundation\Application
{
    /**
     * {@inheritDoc}
     * @see \app\foundation\Application::init()
     */
    public function init()
    {
        // TODO Auto-generated method stub
        parent::init();
    }
    
    public function run()
    {
        $data = \Yii::$app->request->post();
    
        $this->accessContext = new AccessContext();
        $this->accessContext->api = $data['api'];
    
        if(($ret = $this->validate($data)) !== true)
        {
            $this->response->data = ['ret' => $ret, 'msg' => (string)ApiCode::getMsg($ret)];
        }
        else
        {
            $apiModel = \app\models\Api::findOne(['module_id'=>$this->id, 'name'=>$data['api']]);
            if($apiModel && $apiModel->need_login && !$this->loginByAccessToken($data['token'], $data['appid']))
            {
                
                $this->response->data = ['ret' => ApiCode::LOGIN_FAILED, 'msg' => (string)ApiCode::getMsg(ApiCode::LOGIN_FAILED)];
                $this->response->send();
                return;
            }
    
            // 权限验证
            $permissionName = $data['api'];
            if($apiModel->need_login && !\Yii::$app->user->can($permissionName) && \Yii::$app->getErrorHandler()->exception === null){
                throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
            }
            $classname = $this->apiClassName($data['api'], isset($data['v']) ? $data['v'] : null);
            if(!$apiModel || !class_exists($classname))
            {
                $this->response->data = ['ret' => ApiCode::API_NOT_EXIST, 'msg' => (string)ApiCode::getMsg(ApiCode::API_NOT_EXIST)];
            }
            else
            {
                $apiObj = new $classname();
    
                if(!($apiObj instanceof Api))
                {
                    $this->response->data = ['ret' => ApiCode::API_NOT_EXIST, 'msg' => (string)ApiCode::getMsg(ApiCode::API_NOT_EXIST)];
                }
                else
                {
                    $this->response->data['ret'] = $apiObj->ret;
    
                    if($apiObj->ret)
                    {
                        $this->response->data['msg'] = $apiObj->msg;
                    }
    
                    $this->response->data = array_merge($this->response->data, $apiObj->run());
                }
            }
        }
    
        $this->response->send();
    }
    
    /**
     * 用户通过access token登录系统
     *
     * @param string $token
     * @return boolean
     */
    protected function loginByAccessToken($token, $appid)
    {
        if(($user = Identity::findIdentityByAccessToken($token, $appid)) != null)
        {
            return \Yii::$app->user->login($user);
        }
    
        return false;
    }
    
}