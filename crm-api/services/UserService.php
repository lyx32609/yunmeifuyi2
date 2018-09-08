<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use  yii\db\Query;
class UserService extends Service
{
   /**
     * 用户修改密码
     * @parm string password 用户密码
     * @return array 
     * @author lzk
     */
    public function updatePassword($password)
    {
        if(!$password)
        {
            $this->setError('密码不能为空!');
            return false;
        }
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $user->password = md5($password);
        if(!$user->save())
        {
            $this->setError('修改失败', $user->errors);
            return false;
        }
        return true;
    }
    
    
    /**
     *  获取当前部门所有员工
     * @parm int uid 用户id
     * @return array
     * @author qizhifei
     */
    public function getDepartmentStaff($uid){
    	if(!$uid){
    		$this->setError('员工编号不能为空!');
    		return false;
    	}
     	$user = User::findBySql('select * from off_user where department_id = (select department_id from off_user where id = '.$uid.')')
     			->asArray()
     			->all();
     	if(!$user) {
     		$this->setError('当前部门没有人员信息');
     		return false;
     	}
    	return $user;
    }
    
    
    
    
}