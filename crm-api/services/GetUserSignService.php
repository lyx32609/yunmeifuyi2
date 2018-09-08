<?php
namespace app\services;

use app\foundation\Service;

use app\models\UserSign;
use app\models\User;
use yii\data\Pagination;
class GetUserSignService extends Service
{
    /**
     * 获取签到记录
     * @param unknown $type
     * @param unknown $user_id
     * @param unknown $start
     * @param unknown $end
     * @return boolean|\yii\db\ActiveRecord[]
     */
    public function getUserSign($type, $user_id, $start, $end, $page, $pageSize, $user_name)
    {
        if(!$user_id){
            $user = User::findOne(['username' => $user_name]);
            if(!$user){
                $this->setError('用户不存在');
                return false;
            }
            $user_id = $user->id;
        }
        if(!$start && !$end){
            $start = strtotime(date("Y-m-d"),time());
            $end = time();
        }
        if($type == '0'){
            $list = UserSign::find() 
                    ->select(['id', 'time', 'user', 'is_late', 'path', 'image', 'type', 'is_late_time'])
                    ->where(['user' => $user_id])
                    ->andWhere(['between', 'time', $start, $end])
                    ->orderBy('time desc');
        } else {
            $list = UserSign::find()
                    ->select(['id', 'time', 'user', 'is_late', 'path', 'image', 'type', 'is_late_time'])
                    ->where(['user' => $user_id])
                    ->andWhere(['between', 'time', $start, $end])
                    ->andWhere(['is_late' => $type])
                    ->orderBy('time desc');
        }
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageSize,
            'totalCount' => $list->count(),
        ]);//分页参数
        
        $result = $list->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()
                ->all();
        
        for($i = 0; $i < count($result); $i++){
            $result[$i]['is_late'] ? $result[$i]['is_late'] : '';
            $result[$i]['is_late_time'] ? $result[$i]['is_late_time'] : '';
            $result[$i]['path'] ? $result[$i]['path'] : '';
        }
        if(!$result){
            $this->setError('暂无数据');
            return false;
        }
            return $result;
    }
}