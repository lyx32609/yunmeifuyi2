<?php
namespace app\services;

use app\foundation\Service;

class PushMessageService extends Service
{

    /**
     * 推送
     * @param  [type] $cid     [cid集合]
     * @param  [type] $title   [标题]
     * @param  [type] $message [内容]
     * @param  [type] $logo    [logo]
     * @param  [type] $logoUrl [logoUrl]
     * @return [type]          [description]
     */
    public function push($appkey, $appid, $masterSecret,$cidList, $title, $connect)
    {
        $token = json_decode($this->auth($appid, $appkey, $masterSecret), true); //获取token 
        if(!$token['auth_token']){ 
            $this->setError('token获取失败');
            return false;
        } 
        $save_list = json_decode($this->pushMessage($token['auth_token'], $appid, $appkey, $connect), true); //个推服务器存储数据 获取taskid
        if(!$save_list['taskid']){
            $this->setError('taskid获取失败');
            return false;
        } 
        $result = json_decode($this->pushCid($appid, $cidList, $token['auth_token'], $save_list['taskid']), true);
        if(!$result['result'] == 'ok'){
            $this->setError('推送失败');
            return false;
        } 
        return $data = 'ok'; //1代表推送成功
    }
    /**
     * 鉴权
     * @param  [type] $appid        [description]
     * @param  [type] $appkey       [description]
     * @param  [type] $masterSecret [description]
     * @return [type]               [description]
     */
    public function auth($appid, $appkey, $masterSecret){
        $header = array("Content-Type: application/json; charset=utf-8");
        $url = 'https://restapi.getui.com/v1/'. $appid .'/auth_sign';
        $time = $this->getmillisecond();
        $param = [
            'sign' => bin2hex(hash('sha256', $appkey . $time . $masterSecret, true)),
            'timestamp' => $time ,
            'appkey' => $appkey,
        ];
        $data = $this->getToken($header, $url, $param);
        return $data;
    }
    /**
     * 指定用户推送
     * @param  [type] $appid   [description]
     * @param  [type] $cidList [description]
     * @param  [type] $token   [description]
     * @param  [type] $taskid  [description]
     * @return [type]          [description]
     */
    public function pushCid($appid, $cidList, $token, $taskid)
    {
        $header[0] = "Content-Type: application/json";
        $header[1] = "authtoken:" .$token;
        $url = 'https://restapi.getui.com/v1/'. $appid .'/push_list'; 
        $param = [
            // 'cid' => $cidList,
            'alias' => $cidList,
            'taskid' => $taskid,
            'need_detail' => true,
        ]; 
        $data = $this->getToken($header, $url, $param);
        return $data;
    }
    /**
     * 别名绑定
     * @param  [type] $token [description]
     * @param  [type] $appid [description]
     * @return [type]        [description]
     */
    public function bind($token, $appid, $cid, $username)
    {
        $header[0] = "Content-Type: application/json";
        $header[1] = "authtoken:" .$token;
        $url = 'https://restapi.getui.com/v1/'. $appid .'/bind_alias';
        $param = [
            'alias_list' =>[[
                'cid' => $cid,
                'alias' => $username
            ]],
        ];
        $result = $this->getToken($header, $url, $param);
        return $result;
        
    }
    /**
     * 解除绑定
     * @param  [type] $token [description]
     * @param  [type] $appid [description]
     * @return [type]        [description]
     */
    public function checkBind($token, $appid, $username)
    {
        $header[0] = "Content-Type: application/json";
        $header[1] = "authtoken:" .$token;
        $url = 'https://restapi.getui.com/v1/'. $appid .'/unbind_alias_all';
        $param = [
            'alias' => $username,
        ];
        $result = $this->getToken($header, $url, $param);
        return $result;
        
    }
    /**
     * 往个推服务器推送消息
     * @return [type] [description]
     */
    public function pushMessage($token, $appid, $appkey, $connect)
    {
        $header[0] = "Content-Type: application/json";
        $header[1] = "authtoken:" .$token;
        $url = 'https://restapi.getui.com/v1/'. $appid .'/save_list_body'; 
        $param = [
            'message' => [
                'appkey' => $appkey,
                'is_offline' => true,
                'msgtype' => 'transmission',
                'offline_expire_time' => 10000000,
            ],
            'transmission' => [
                'transmission_content' => $connect,
            ],
            'push_info' => [
                'aps' => [
                    'alert' => [
                        'title' => '',
                        'content' => $connect,
                        'body' => $connect,
                        
                    ],
                    'sound' => 'default'
                ]
            ],
        ];
        $data = $this->getToken($header, $url, $param);
        return $data;
    }
    public function selectCid($token, $appid, $username)
    {
        $header[0] = "Content-Type: application/json";
        $header[1] = "authtoken:" .$token;
        $postUrl = 'https://restapi.getui.com/v1/'. $appid .'/query_cid/'.$username;
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);           //设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch); 
        return $data;
    }
    //推送的Curl方法
    public function getToken($header, $url, $param) 
    {
        $postUrl = $url;
        $curlPost = json_encode($param);
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);           // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch); 
        return $data;
    }
    /**
     * 获取毫秒
     * @return [type] [description]
     */
    public function getmillisecond() { 
        list($s1, $s2) = explode(' ', microtime()); 
        return (string)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
    }
}