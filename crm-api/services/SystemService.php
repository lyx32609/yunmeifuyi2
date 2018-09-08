<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 16:04
 */
namespace app\services;

use app\foundation\Service;
use app\models\SystemRecord;
use app\models\User;

class SystemService extends Service
{
    /**
     * @param $user_id 用户id
     * @param $message 错误信息
     * @param $time  时间
     * @param $phone 手机
     * @return bool
     * 存储系统 错误返回信息
     */
    public function addBugRecord($user_id, $message, $time, $phone, $type)
    {
        $user = User::find()->where(['id'=>$user_id])->one();
        $record = new SystemRecord();
        $record->staff_num = $user->username;
        $record->content = $message;
        $record->time = $time;
        $record->type = $type;
        $record->brand_model = $phone;
        if ($record->save()){
            return true;
        }else{
            return false;
        }
    }
}