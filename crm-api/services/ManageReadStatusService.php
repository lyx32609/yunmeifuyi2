<?php

namespace app\services;


use app\foundation\Service;
use app\models\Examine;

class ManageReadStatusService extends Service
{
    /**
     * @param $user_id 用户ID
     * @return bool
     * 获取审批人是否有待审的签呈，返回状态
     */
    public function gerReadStutas($user_id){
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $data = Examine::find()
            ->leftJoin("off_petition",'off_examine.petition_id = off_petition.id ')
            ->where(['off_examine.uid'=>$user_id,'off_examine.status'=>'2','off_examine.is_visible'=>'1',"off_petition.is_show"=>'1'])
            ->asArray()
            ->all();
        if(!$data){
            $result['read_status'] = '2';
            $result['description'] = '该用户无待审批签呈';
        }
        else{
            $result['read_status'] = '1';
            $result['description'] = '该用户有待审批签呈';

        }
        return $result;

    }

}