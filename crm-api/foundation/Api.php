<?php
namespace app\foundation;

use app\models\ApiCode;

/**
 * @author ldj
 */
abstract class Api
{
   /**
    * @var int 返回码,0表示无错误
    */
   public $ret = 0;
   
   /**
    * @var string 保存错误消息
    */
   public $msg = '';
   
   public abstract function run();
   
   /**
    * 设置错误消息
    * 
    * @param int $code
    * @param string $msg
    */
   protected function error($code, $msg = '', $detail = [])
   {
       $this->ret = $code;
       
       if(empty($msg))
       {
            $msg = ApiCode::getMsg($code);
       }
       
       $this->msg = $msg;
       
       return ['ret'=>$code, 'msg'=>$msg, 'detail'=>$detail];
   }
   
   /**
    * 设置一般逻辑性错误的消息
    * 
    * @param string $msg
    */
   protected function logicError($msg, $details = [])
   {
       return $this->error(ApiCode::LOGIC_ERROR, $msg, $details);
   }
   
   /**
    * 将Model的错误信息数组转换成以逗号分隔的字符串表示形式
    * 
    * @param array $errors 错误消息数组
    * @return stirng
    */
   public static function modelErrorsToString($errors)
   {
       foreach ($errors as $k => $v)
       {
           $arr[] = implode(',', $v);
       }
       return implode(',', $arr);
   }
   
}