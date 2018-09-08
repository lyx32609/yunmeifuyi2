<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\Petition;
use app\models\Examine;
use app\foundation\JPush\Client;
use app\models\JpushLog;

class PetitionNewTwoService extends Service
{
    /*
     * @param $user_id  提报人id
     * @param $master_img 图片
     * @param $file 附件
     * @param $ids  审批人
     * @param $type 签呈类型15种
     * 0通用  1领用  2用车  3付款  4报销  5采购  6用证  7用印  8出差  9加班  10外出  11转正  12离职  13请假  14招聘
     * @param $message  签呈的信息
     *
     * 增加初始化字段 flag  是否到该人审核
     * @return bool|string
     */
    public function addPetitionNewType($user_id,$master_img,$file,$ids,$type,$message)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        $user = User::find()
            ->select(['name', 'department_id', 'company_categroy_id'])
            ->where(['id' => $user_id])
            ->asArray()
            ->one();
        $result = new Petition;
        $result->uid = $user_id;
        $result->message = $message;
        $result->master_img = $master_img;
        $result->file = $file;
        $result->type = $type;
        $result->company_id = $user['company_categroy_id'];
        $result->department_id = $user['department_id'];
        $result->create_time = time();
        $result->status = 3;  //0 签呈的状态审核中
        $result->ids = $ids;
        if($result->save()) {
            $pass_id = explode(',', $ids);
            foreach ($pass_id as $key => $value) {
                $examine = new Examine;
                $examine->petition_id = $result->id;
                //增加初始化字段 flag  是否到该人审核
                if ($key == 0){
                    $examine->is_visible = 1;
                    $examine->flag = 1;
                }else{
                    $examine->is_visible = 2;
                    $examine->flag = 2;
                }
                $examine->uid = $value;
                $examine->status = 2;   //单个人是待审状态
                $examine->tag = 1;   //默认不是转签来的
                $res = $examine->save();
            }
            if ($res) {
                /* 新增推送部分 */
                $push = $this->Jpush(current($pass_id), '签呈提报推送',1);
                if ($push){
                    $log = $this->jpushLog(current($pass_id), '签呈提报推送');
                    if ($log){
                        return '签呈提交成功';
                    }else {
                        $this->setError('推送添加日志失败！');
                        return false;
                    }
                }else{
                    $this->setError('推送失败！');
                    return false;
                }
                /* 新增推送部分 */
            }
        }else{
            $this->setError('签呈提交失败');
            return false;
        }
    }
    /*
    * 审核签呈  增加修改一个字段 flag
    *
    * */
    public function DealPetitionNew($user_id,$petition_id,$advice,$status)
    {
        if (!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if (!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        //不同意   修改examine的自己的状态，并且修改整个签呈的状态 为不同意
        if ($status == 0){
            $res = Examine::find()->where(['uid'=>$user_id,'petition_id'=>$petition_id])->one();
            $res->advice = $advice;
            $res->status = 0; //单个审批人对签呈的状态 0 不同意
            $res->examine_time = time();
            $res->flag = 2;  // flag 2 为不可再审核
            if ($res->save()){
                $petition = Petition::find()->where(['id'=>$petition_id])->one();
                //判断是否是付款签呈
                if (in_array($petition['type'], [3,4,5])){
                    $petition->status = 6;   //付款签呈 签呈状态 为已完成未支付6
                }else{
                    $petition->status = 4;  //整个签呈显示已完成状态  不是付款签呈改为已完成4
                }
                $petition->pass_time = time();
                if (!$petition->save()){
                    $this->setError('审批失败！');
                    return false;
                }else{
                    return '审批成功！';
                }
            }else{
                $this->setError('审批失败！');
                return false;
            }
        //同意 获取下一个审批人   同意 该签呈 修改examine自己的状态，然后将下一个审批人的可见状态修改为可见 可审核
        }elseif ($status == 1){
            $res = Examine::find()->where(['uid'=>$user_id,'petition_id'=>$petition_id])->one();
            $petition1 = Petition::find()->where(['id'=>$petition_id])->one();
            //判断是否是付款签呈   3付款4报销5采购
            if (in_array($petition1['type'], [3,4,5])){
                $res->status = 4;   //付款签呈 单个审批人的状态 为同意未支付4
            }else{
                $res->status = 1;    //不是付款签呈改为同意
            }
            $res->advice = $advice;
            $res->examine_time = time();
            $res->flag = 2;    // flag 2 为不可再审核
            if ($res->save()){
                $ids = Petition::find()->select('ids')->where(['id'=>$petition_id])->asArray()->one();
                //将审批人列表 遍历 数组
                $ids_arr = explode(',',$ids['ids']);
                $key = array_search($user_id, $ids_arr);
                $next_id = $ids_arr[$key + 1 ];
                if (empty($next_id)){
                    //全部同意，修改整个的签呈状态
                    $petition = Petition::find()->where(['id'=>$petition_id])->one();
                    //判断是否是付款签呈
                    if (in_array($petition['type'], [3,4,5])){
                        $petition->status = 6;   //付款签呈 签呈状态 为已完成未支付6
                    }else{
                        $petition->status = 4;  //整个签呈显示已完成状态  不是付款签呈改为已完成4
                    }
                    $petition->pass_time = time();
                    if ($petition->save()){
                        return '审批成功！';
                    }else{
                        $this->setError('审批失败！');
                        return false;
                    }
                }else{
                    //一个同意，让下一个人可见 可审核
                    $result = Examine::find()->where(['uid'=>$next_id,'petition_id'=>$petition_id])->one();
                    $result->is_visible = 1;
                    //增加初始化字段 flag  是否到该人审核
                    $result->flag = 1;
                    if ($result->save()){
                        /* 新增推送部分 */
                        $push = $this->Jpush($next_id, '签呈流转推送',2);
                        if ($push){
                            $log = $this->jpushLog($next_id, '签呈流转推送');
                            if ($log){
                                return '审批成功！';
                            }else {
                                $this->setError('推送添加日志失败！');
                                return false;
                            }
                        }else{
                            $this->setError('推送失败！');
                            return false;
                        }
                        /* 新增推送部分 */
                    }else{
                        $this->setError('审批失败！');
                        return false;
                    }
                }
            }
        }
    }

    /*
    *作废签呈
    create_time 2018.05.17
    by fulamei
    */
    public function InvalidPetition($id,$invalid_description)
    {
        if(!$id)
        {
            $this->setError("签呈id不能为空");
            return false;
        }
        if(!$invalid_description)
        {
            $this->setError("作废描述不能为空");
            return false;
        }
        $petition = Petition::findOne(["id"=>$id]);
        if($petition)
        {
            $petition->status = 7;
            $petition->invalid_description = $invalid_description;//添加作废描述（必填）
            if($petition->save())
            {
                return '作废成功';
            }
            else
            {
                $this->setError("作废失败");
                return false;
            }
        }
        else
        {
                $this->setError("签呈不存在");
                return false;
        }
        


    }

    /**
     * @param $petition_id 签呈id
     * @return bool
     * 签呈审核提醒接口
     */
    public function warnPetition($petition_id)
    {
        $petition = Petition::find()->where(['id'=>$petition_id])->one();
        if (!$petition){
            $this->setError("签呈不存在");
            return false;
        }
        $warn =  $petition->warn_time;
        if (empty($warn)){
            $ret = $this->WarnPush($petition_id, $petition);
            if ($ret){
                return '已发送审核提醒';
            }else{
                $this->setError("推送失败！");
                return false;
            }
        }else{
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            $now = $petition->warn_time;
            if ($now >= $beginToday && $now <= $endToday){
                $this->setError("本日已发出审核提醒！");
                return false;
            }else{
                $ret = $this->WarnPush($petition_id, $petition);
                if ($ret){
                    return '已发送审核提醒';
                }else{
                    $this->setError("推送失败！");
                    return false;
                }
            }
        }
    }
    /**
     * @param $petition_id 签呈iD
     * @param $petition    签呈
     * @return bool
     * 推送 并且 重写当前时间  warn_time
     */
    public function WarnPush($petition_id, $petition)
    {
        $people =  Examine::find()
            ->where(['petition_id'=>$petition_id])
            ->andWhere(['flag'=>1])
            ->asArray()
            ->one();
        if (!$people){
            return false;
        }
        $push = $this->Jpush($people['uid'], '签呈审核提醒推送',5);
        if ($push){
            $log = $this->jpushLog($people['uid'], '签呈审核提醒推送');
            if ($log){
                $petition->warn_time = time();
                if ($petition->save()){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
        /**
     * @param $receive  接收推送的对象
     * @return bool
     * 极光推送
     */
    public function Jpush($receive, $content, $type = '')
    {
        $app_key = \Yii::$app->params['jpush_appkey'];
        $master_secret = \Yii::$app->params['jpush_secret'];
        $client = new Client($app_key, $master_secret);
        $push = $client->push();
        $push->setPlatform(['ios', 'android']);
        $push->addAlias($receive);
        $push->androidNotification($content, ['extras' =>['badge'=>'1','type'=> $type]]);
        $push->iosNotification($content, ['sound' => 'sound', 'badge' => '+1','extras' => ['type'=> $type]]);
        $res = $push->send();
        if ($res['http_code'] == 200){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @param $receive  推送 接收人
     * @param $content  推送内容
     * @return bool
     * 推送添加日志
     */
    public function jpushLog($receive, $content)
    {
        $log = new JpushLog();
        $log->receive = $receive;
        $log->time = time();
        $log->content = $content;
        if ($log->save()){
            return true;
        }else{
            return false;
        }
    }
}
