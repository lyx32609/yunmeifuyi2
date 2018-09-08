<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 9:45
 */
namespace components;

use yii\web\Controller;
use Yii;
class BaseController extends Controller
{
    /**
     * 成功提示
     * @param type $msg 提示信息
     * @param type $jumpurl 跳转url
     * @param type $wait 等待时间
     */
     function  success($msg="",$jumpUrl="",$wait=3){
       return $this->_jump($msg, $jumpUrl, $wait, 1);
    }
    /**
     * 错误提示
     * @param type $msg 提示信息
     * @param type $jumpurl 跳转url
     * @param type $wait 等待时间
     */
     function  error($msg="",$jumpUrl="",$wait=3){
         return $this->_jump($msg, $jumpUrl, $wait, 0);
    }
    /**
     * 最终跳转处理
     * @param type $msg 提示信息
     * @param type $jumpurl 跳转url
     * @param type $wait 等待时间
     * @param int $type 消息类型 0或1
     */
    private  function  _jump($msg="",$jumpUrl="",$wait=3,$type=0){
        $data = array('msg' => $msg, 'jumpurl' => $jumpUrl, 'wait' => $wait, 'type' => $type);
        $data['title'] = ($type==1) ? "提示信息" : "错误信息";

        if(empty($jumpUrl)){
            if($type==1){
                $data['jumpUrl']=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"javascript:window.close();";
            }else{
                $data['jumpUrl'] = "javascript:history.back(-1);";
            }
        }else{
            $data['jumpUrl'] =  Yii::$app->getUrlManager()->createUrl($jumpUrl);
        }
       return $this->render("/layouts/show_message",$data);
    }
}