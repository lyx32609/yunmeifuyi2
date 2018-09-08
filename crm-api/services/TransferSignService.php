<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 10:29
 */
namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\Petition;
use app\foundation\JPush\Client;
use app\models\JpushLog;

class TransferSignService extends Service
{
    public function TransferSign($user_id,$petition_id,$advice,$tranfer_id)
    {

        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        if(!$advice) {
            $this->setError('审批意见不能为空');
            return false;
        }
        if(!$tranfer_id) {
            $this->setError('转签人ID不能为空');
            return false;
        }
        $petition =  Petition::find()
            ->select('uid,ids')
            ->where(['id'=>$petition_id])
            ->one();
        $ids = $petition['ids'];
        //原审批人数组
        $arr_ids = explode(',',$ids);
        //该审批人所在的key
        $k =  array_search($user_id, $arr_ids);
        if ($k === false){
            $this->setError('没有该审批人！');
            return false;
        }
        $tranfer_arr = explode(',', $tranfer_id);
        //判断转签人中是否有已在审批进程的人
        foreach ($tranfer_arr as $key =>$value)
        {
            if (in_array($value, $arr_ids)){
                $this->setError('已有转签人在审批进程中！');
                return false;
            }
        }
        //判断转签人中是否有发起人
        if (in_array($petition['uid'], $tranfer_arr)){
            $this->setError('发起人不可以为转签人！');
            return false;
        }
        //往当前审批人以后   增加转签的审批人数组
        array_splice($arr_ids,$k+1,0,$tranfer_arr);
        $new_ids = implode(',', $arr_ids);
        //更新签呈表中审批人的字段
        $qian = Petition::find()->where(['id'=>$petition_id])->one();
        $qian->ids = $new_ids;
        if ($qian->save()){
            $examine = Examine::find()
                ->where(['uid'=>$user_id])
                ->andWhere(['petition_id'=>$petition_id])
                ->one();
            //转签之后自己的状态变成 已转签 更新 审批意见 不可在审核flag=2
            $examine->status = 5;
            $examine->advice = $advice;
            $examine->examine_time = time();
            $examine->flag = 2;
            if ($examine->save()){
                //循环往审批人列表中中增加审批人
                foreach ($tranfer_arr as $k =>$v)
                {
                    $tranfer_examine = new Examine();
                    $tranfer_examine->petition_id = $petition_id;
                    $tranfer_examine->uid = $v;     // 转签人的id
                    $tranfer_examine->status = 2;   //待审状态
                    $tranfer_examine->tag = 2;      //是转签过来的标志
                    if ($k == 0){
                        //第一个人可以见  可以 审核
                        $tranfer_examine->is_visible = 1;
                        $tranfer_examine->flag = 1;
                    }else{   //之后的人不可见 不可审核
                        $tranfer_examine->is_visible = 2;
                        $tranfer_examine->flag = 2;
                    }
                    $res = $tranfer_examine->save();
                }
                if ($res) {
                    /* 新增推送部分 */
                    $push = $this->Jpush(current($tranfer_arr), '签呈转签推送',4);
                    if ($push){
                        $log = $this->jpushLog(current($tranfer_arr), '签呈转签推送');
                        if ($log){
                            return '转签成功！';
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
                    $this->setError('转签失败3！');
                    return false;
                }
            }else{
                $this->setError('转签失败2！');
                return false;
            }
        }else{
            $this->setError('转签失败1！');
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
        $push->androidNotification($content, ['extras' =>['badge'=>'1','type'=>$type]]);
        $push->iosNotification($content, ['sound' => 'sound', 'badge' => '+1','extras'=>['type'=>$type]]);
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