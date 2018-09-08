<?php
namespace app\foundation;

use yii\base\Object;
/**
 * 逻辑服务层基础类
 * @author ldj
 * 
 * @property string $error
 * @property array $errors
 */
class Service extends Object
{
    protected static $_instances = [];
    
    /**
     * @var array 详细错误消息
     */
    protected $_errors;
    
    /**
     * @var string 错误消息
     */
    protected $_error;
    
    /**
     * 获取自身的一个实例
     * @return static
     */
    public static function instance()
    {
        $class_name = self::className();
        
        if(!(isset(self::$_instances[$class_name])))
        {
            self::$_instances[$class_name] = new $class_name();
        }
        
        return self::$_instances[$class_name];
    }
    
    public function setError($error, $details = [])
    {
        $this->_error = $error;
        $this->_errors = $details;
    }
    
    public function getError()
    {
        return $this->_error;
    }
    
    /**
     * 返回错误的详细信息
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}