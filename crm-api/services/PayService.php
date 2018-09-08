<?php


namespace app\services;


use app\foundation\Service;
use app\models\Examine;
use app\models\Petition;


class PayService extends Service
{
    /*签呈类型type:3付款4报销5采购
     * 支付*/
    public function petitionPay($user_id, $petition_id)
    {
        if (!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }

        $result = Petition::find()
            ->where(['id' => $petition_id])
            ->andWhere(['in', 'type', [3, 4, 5]])
            ->one();
        $rank = explode(',', $result['ids']);
        $last = end($rank);  //获取数组的最后一个元素
        $data = Examine::find()
            ->where(['petition_id' => $petition_id, 'uid' => $user_id])
            ->one();
        if (!$data) {
            $this->setError('没有符合条件的审批签呈');
            return false;
        }else {
            $data->status = 3;
            if (!$data->save()) {
                $this->setError('审批人签批支付状态修改失败');
                return false;
            }
            if ($user_id != $last) {
                return "审批人支付状态修改成功";
            }
        }
        if (!$result) {
            $this->setError('该签呈不符合支付签呈类型');
            return false;
        }
        if ($user_id == $last) {
            $result->status = 5;
            if (!$result->save()) {
                $this->setError('签呈支付状态修改失败');
                return false;
            }
            return "签呈支付状态修改成功";
        }


    }

}