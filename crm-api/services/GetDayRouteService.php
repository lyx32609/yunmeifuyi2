<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserSign;
use app\models\User;
use Yii;
class GetDayRouteService extends Service
{
    /**
     * 获取用户某天签到记录（定位）
     * @param $user 用户ID
     * @param $date 传递的时间参数（即哪一天的签到记录）
     * @return array|bool
     */
    public function getUserRoute($user,$date)
    {
        if(!$user) {
            $this->setError('用户id不能为空');
            return false;
        }else{
            $is_user = User::findOne(['id' => $user]);
            if(!$is_user){
                $this->setError('用户不存在');
                return false;
            }

        }
        if(!$date){
            $date = date('Y-m-d');
        }
        $first_time = $date ." 00:00:00";
        $first_date = strtotime($first_time);
        $last_time = $date ." 23:59:59";
        $last_date = strtotime($last_time);

        $result_data = UserSign::find()
        ->select(['user','longitude','latitude','time'])
        ->where(['between','time',$first_date,$last_date])
        ->andWhere('user = :user',[':user' => $user])
        ->orderBy('time asc')
        ->asArray()
        ->all()
        ;
        $result = [msg => $result_data];
        if(!$result_data) {
            $this->setError('当天没有人员签到信息');
            return false;
        }
        return $result;
    }

}