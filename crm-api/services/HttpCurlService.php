<?php
namespace app\services;

use Yii;
use app\foundation\Service;
class HttpCurlService extends Service
{
	/**
	 * 调取对接数据
	 * @param  [type] $url    [description]
	 * @param  [type] $header [description]
	 * @param  [type] $param  [description]
	 * @param  [type] $key    [description]
	 * @return [type]         [description]
	 */
	public function request($url, $key,  $param = null,  $protocol = 'https',$header = array("Content-Type: application/json; charset=utf-8"))
	{

		$app_key = hash('sha256', $key);
		$app_header = array($header);
		$app_url = $protocol. '://' . $url;
		// $app_param = json_encode($param);
		$app_param = $param;
		$result = $this->uploadServer($app_url, $app_header, $app_param, $app_key);
		$data = json_decode($result, true);
		if($data['ret'] == 0){ 
			return $data['result'];
		}
		return false;
	}

	/**
	 * 对接
	 * @param  [type] $header [description]
	 * @param  [type] $url    [description]
	 * @param  [type] $param  [description]
	 * @return [type]         [description]
	 */
	private function uploadServer($url, $header, $param, $key) 
    {
        $postUrl = $url;
        
        // $curlPost = json_encode($param);
        $param['key'] = $key;
        // $curlPost = json_encode($param);
        $curlPost = self::makeQueryString($param);
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, false);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);           // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch); 
        return $data;
    }
    static public function makeQueryString($params)
	{
		if (is_string($params)){
			return $params;
		}
			
		$query_string = array();
	    foreach ($params as $key => $value)
	    {   
	        array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
	    }   
	    $query_string = join('&', $query_string);
	    return $query_string;
	}
}