<?php
namespace app\foundation;

use app\models\ApiClient;
use app\models\ApiCode;


abstract class Application extends \yii\web\Application
{
   
    public $version = 1;
    public $defaultApp = 'purchaser';
    public $accessContext = null;

    /**
     * {@inheritDoc}
     * @see \yii\base\Application::init()
     */
    public function init()
    {
        // TODO Auto-generated method stub
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
    
    protected function apiClassName($api, $v = null)
    {

        $classname = '';
        $api = explode('/', $api);
        
        if($v && $v <= \Yii::$app->version) // 首先尝试加载指定版本的的接口
        {
            $classname = $this->id . "\\api\\{$api[0]}\\v{$v}\\" . ucfirst($api[1]).'Api';
            if(!class_exists($classname, true))
            {
                $classname = '';
            }
        }
    
        if(!$classname) // 指定版本不存在，加载最新版本
        {
            $classname = $this->id . "\\api\\{$api[0]}\\" . ucfirst($api[1]).'Api';
        }

        return $classname;
    }

    /**
     * 用户通过access token登录系统
     * 
     * @param string $token
     * @return boolean
     */
    abstract protected function loginByAccessToken($token, $appid);
    
    /**
     * 参数合法性验证
     */
    protected function validate(&$params)
    {
        
        $sigStr = '';
        $s = '';
        $appid = '';

        if(isset($params['s']))
        {
            $s = $params['s'];
            unset($params['s']);
        }
    
        if(isset($params['appid']))
        {
            $appid = $params['appid'];
        }
        
        ksort($params);
       
        $clientInfo = ApiClient::findOne($appid);

        if(!$clientInfo || $clientInfo['module_id'] != \Yii::$app->id)
        {
            return ApiCode::APPID_NOT_EXIST;
        }
    
        $appkey = $clientInfo['appkey'].'&';
       
        if((time() - $params['t']) > \Yii::$app->params['max_time_difference']) // 客户端与服务器时间差检测
        {
            return ApiCode::TIME_DIFFERENCE_OVERFLOW;
        }
    
        foreach($params as $key=>$item)
        {
            $sigStr .= '&'.$key.'='.$item;
        }
        
        $signature = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($appkey, '-_', '+/'), true));
       /*  if($_POST['api'] == 'file/upload')
        {
            file_put_contents('/mydata/webroot/dev.api.xunmall.com/web/assets/upload.html', '<h1>时间：'.date('Y-m-d H:i:s').'</h1><br />服务器生成的签名'.$signature.'<br /><pre>get:'.print_r($_GET, true).'<br />post:'.print_r($_POST, true).'file:'.print_r($_FILES, true).'</pre>', FILE_APPEND);
        }

        file_put_contents('/mydata/webroot/dev.api.xunmall.com/web/assets/log.html', '<h1>时间：'.date('Y-m-d H:i:s').'</h1><br>'
            .'<h1>调整顺序后-params: </h1><br>'
            .print_r($params, true).'<br>'
            .'<h1>$sigStr:</h1><br>'.$sigStr.'<br>'
            .'<h1>urlencode($sigStr):</h1><br>'
            .urlencode($sigStr).'<br>'
            .'<h1>$appKey:</h1><br>'
            .$appkey.'<br>'
            .'<h1>服务器端签名:</h1><br>'
            .$signature.'<br>'
            .'<h1>传入签名:</h1><br>'
            .$s.'<br>'
            , FILE_APPEND); */

       
        if($s != $signature)
        {
            // return $result['1'] = [
            //     'params' => $params,
            //     'appkey' => $appkey,
            //     's' => $s,
            //     'signature' => $signature,
            //     'sigStr' => $sigStr,
            //     'urlencode' => urlencode($sigStr),
            // ];
            return ApiCode::SIGNATURE_ERROR;
        }
    
        $this->accessContext->appid = $$appid;
        $this->accessContext->appkey = $clientInfo['appkey'];
        return true;
    }
}