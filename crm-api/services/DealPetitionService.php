<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:48
 */
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\Petition;
use app\models\Examine;

class DealPetitionService extends Service
{
    public function DealPetition($user_id,$petition_id,$advice,$status)
    {
        if (!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if (!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        if (!$advice) {
            $this->setError('审批意见不能为空');
            return false;
        }
        //不同意   修改examine的自己的状态，并且修改整个签呈的状态 为不同意
        if ($status == 0){
            $res = Examine::find()->where(['uid'=>$user_id,'petition_id'=>$petition_id])->one();
            $res->advice = $advice;
            $res->status = $status;
            $res->examine_time = time();
            if ($res->save()){
                $petition = Petition::find()->where(['id'=>$petition_id])->one();
                $petition->status = 0;
                $petition->pass_time = time();
                if (!$petition->save()){
                    return '审批失败！';
                }else{
                    return '审批成功！';
                }
            }else{
                return false;
            }
            //获取下一个审批人   同意 该签呈 修改examine自己的状态，然后将下一个审批人的可见状态修改为可见
        }elseif ($status ==1){
            $res = Examine::find()->where(['uid'=>$user_id,'petition_id'=>$petition_id])->one();
            $res->advice = $advice;
            $res->status = $status;
            $res->examine_time = time();
            if ($res->save()){
                $ids = Petition::find()->select('ids')->where(['id'=>$petition_id])->asArray()->one();
                //将审批人列表 遍历 数组
                $ids_arr = explode(',',$ids['ids']);
                $key = array_search($user_id, $ids_arr);
                $next_id = $ids_arr[$key + 1 ];
                if (empty($next_id)){
                    //全部同意，修改整个的签呈状态
                    $petition = Petition::find()->where(['id'=>$petition_id])->one();
                    $petition->status = 1;
                    $petition->pass_time = time();
                    if ($petition->save()){
                        return '审批成功！';
                    }else{
                        return '审批失败！';
                    }
                }else{
                    //一个同意，让下一个人可见
                    $result = Examine::find()->where(['uid'=>$next_id,'petition_id'=>$petition_id])->one();
                    $result->is_visible = 1;
                    if ($result->save()){
                        return '审批成功！';
                    }else{
                        return '审批失败！';
                    }
                }
            }
        }
    }
}