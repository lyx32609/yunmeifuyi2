<?php
namespace app\foundation;

use app\benben\NetworkHelper;
class ApiHelper
{
    /**
     * @var spring 服务器地址
     */
    public $server;
    
    /**
     * @var spring 文件上传服务器地址
     */
    public $uploadServer;
    
    /**
     * @var string 协议
     */
    public $protocol = 'http';
    
    /**
     * @var int 服务器分配的应用ID
     */
    public $appid;
    
    /**
     * @var string 密钥
     */
    public $secret;
    
    /**
     * @var string 平台
     */
    public $platform;
    
    /**
     * 发起接口请求
     * 
     * @param string $api 接口名称
     * @param array $params 请求参数
     * @return array
     */
    public function request($api, $params = [])
    {
        $params['api'] = $api;
        $params['appid'] = $this->appid;
        $params['t'] = time();
      //  $params['platform'] = $this->platform;
       
        ksort($params);
        $sigStr = '';

        foreach($params as $key=>$item)
        {
            $sigStr .= '&'.$key.'='.$item;
        }
    //    var_dump($sigStr);exit;
        $params['s'] = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($this->secret.'&', '-_', '+/'), true));
        $response = NetworkHelper::makeRequest($this->server, $params);
  //       echo '<pre>';print_r($response);exit();
        if(!$response['result']) // 发生网络请求错误
        {
            return [
                'ret' => -1, 
                'msg' => $response['msg'],
            ];
        }

        return json_decode($response['msg'], true);
    }
    public function ad_request($api, $params = [])
    {
        $params['appid'] = $this->appid;
        $params['t'] = time();
        //  $params['platform'] = $this->platform;

        ksort($params);
        $sigStr = '';

        foreach($params as $key=>$item)
        {
            $sigStr .= '&'.$key.'='.$item;
        }
        //    var_dump($sigStr);exit;
        $params['s'] = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($this->secret.'&', '-_', '+/'), true));
        $response = NetworkHelper::makeRequest($this->server .'/' . $api, $params);
        //       echo '<pre>';print_r($response);exit();
        if(!$response['result']) // 发生网络请求错误
        {
            return [
                'ret' => -1,
                'msg' => $response['msg'],
            ];
        }
//        return $params;
        return json_decode($response['msg'], true);
    }
    /**
     * @param $api
     * @param array $params
     * @return array|mixed
     * 买买金接口请求方式
     */
    public function mmj_request($api, $params = [])
    {
        $params['api'] = $api;
        $params['appid'] = $this->appid;
        $params['t'] = time();
        ksort($params);
        $sigStr = '';

        foreach($params as $key=>$item)
        {
            $sigStr .= '&'.$key.'='.$item;
        }
        $params['s'] = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($this->secret.'&', '-_', '+/'), true));
        $response = NetworkHelper::makeRequest($this->server, $params);

        if(!$response['result']) // 发生网络请求错误
        {
            return [
                'ret' => -1,
                'msg' => $response['msg'],
            ];
        }

        return json_decode($response['msg'], true);
    }
    /**
     * 发起接口请求
     *
     * @param string $api 接口名称
     * @param array $params 请求参数
     * @return array
     */
    public function upload()
    {
        $params['api'] = 'file/uploads';
        $params['appid'] = $this->appid;
        $params['t'] = time();
//         $params['user'] = $_REQUEST['username'];
        if(\Yii::$app->user->identity)
        {
            $params['token'] = \Yii::$app->user->identity->accessToken;
            $params['uid'] = \Yii::$app->user->identity->id;
        }
    
        ksort($params);
        $sigStr = '';
    
        foreach($params as $key=>$item)
        {
            $sigStr .= '&'.$key.'='.$item;
        }
    
        $params['s'] = base64_encode(hash_hmac('sha1', urlencode($sigStr), strtr($this->secret.'&', '-_', '+/'), true));
    
        $response = NetworkHelper::makeUploadRequest($this->protocol.'://'.$this->uploadServer, $params);
        if(!$response['result']) // 发生网络请求错误
        {
            return [
                'ret' => -1,
                'msg' => $response['msg'],
            ];
        }
    
        return json_decode($response['msg'], true);
    }
}