<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 16:13
 */
namespace app\services;
use app\foundation\Service;
use app\models\User;
use app\models\PutImei;

class PhoneImeiService extends Service
{
    /*
     * 验证串号与账号信息
     *
     * */
    public function verifyImei($user_id, $imei_number)
    {
        if(!$user_id) {
            $this->setError('用户ID不能空');
            return false;
        }
        if(!$imei_number) {
            $this->setError('手机串号不能为空');
            return false;
        }
        //查询手机串号是否有记录
        // $record = User::find()->where(['phone_imei'=>$imei_number])->one();
        $record = User::find()->where(['phone_imei'=>$imei_number])->asArray()->all();
        $record_arr = array_column($record, 'id');
        //查询账号是否有串号记录
        $ret = User::find()->where(['id'=>$user_id])->one();
        //有未审核的提报记录
        $result = PutImei::find()
            ->where(['user_id'=>$user_id])
            ->andWhere(['status'=>1])
            ->all();
        if ($result) {
            return [
                'ret' => 40,
                'msg' => '已提报串号，正在审核中！'
            ];
        }
        //串号无记录
        if (!$record){
            //账号没有对应的串号
            if (empty($ret['phone_imei'])){
                return [
                    'ret' =>20,
                    'msg' =>'该账号未绑定串号，是否绑定？'
                ];
            }else{
                return [
                    'ret' =>30,
                    'msg' =>'已有串号是否修改？'
                ];
            }
        }else{
            //有记录 跟当前账号对应
            if (in_array($user_id, $record_arr)){
                return '匹配成功！';
                //有记录 跟当前账号不对应
            }else{
                return [
                    'ret' =>10,
                    'msg' =>'匹配失败！'
                ];
            }
        }
    }
    /*
     * 账号绑定串号
     * */
    public function batchImei($user_id, $imei_number,$phone_brand)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if(!$imei_number) {
            $this->setError('手机串号不能为空');
            return false;
        }
        if(!$phone_brand) {
            $this->setError('手机品牌不能为空');
            return false;
        }
        $people = User::find()->where(['id'=>$user_id])->one();
        $people->phone_imei = $imei_number;
        $people->phone_brand = $phone_brand;
        $people->imei_time = time();
        if ($people->save()){
            return '绑定成功！';
        }else{
            return '绑定失败！';
        }
    }
    /*
     * 处理串号
     * */
    public function dealImei($user_id, $put_imei_id)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if(!$put_imei_id) {
            $this->setError('提报串号id不能为空');
            return false;
        }
        //找提报的串号id
        $new_imei = PutImei::find()
            ->where(['id'=>$put_imei_id])
            ->one();
        if (!$new_imei){
            return '提报记录未找到！';
        }
        //找原有的串号记录
        $old_imei = User::find()
            ->where(['id'=>$user_id])
            ->one();
        if (!$old_imei){
            return '原串号记录未找到！';
        }
        //更新提报表中的旧串号记录
        $new_imei->pass_time = time();
        $new_imei->status = 2;
        if ($new_imei->save())
        {
            //更新绑定的串号记录
            $old_imei->phone_imei = $new_imei->new_imei_number;
            $old_imei->imei_time = $new_imei->submit_time;
            $old_imei->phone_brand = $new_imei->new_brand;
            if ($old_imei->save()){
                return '成功！';
            }else{
                return '更改原串号失败！';
            }
        }else{
            return '更改新串号失败！';
        }
    }
    /*
     * 查询该用户是否有未读的设备申请记录
     * */
    public function redDot($user_id)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $people = User::find()->where(['id'=>$user_id])->one();
        $res = PutImei::find()
            ->where(['company_categroy_id'=>$people->company_categroy_id])
            ->andWhere(['is_read'=>1])
            ->all();
        if (!$res){
            //没有未读的
            return 1;
        }else{
            //有未读的
            return 2;
        }
    }
}