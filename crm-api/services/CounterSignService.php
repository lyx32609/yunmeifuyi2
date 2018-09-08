<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 10:41
 */
namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\Petition;
use app\foundation\JPush\Client;
use app\models\JpushLog;

/**
 * Class CounterSignService
 * 加签
 * @package app\services
 */
class CounterSignService extends Service
{
    public function CounterSign($user_id,$petition_id,$add_msg)
    {
        if(!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        if(!$add_msg) {
            $this->setError('加签信息不能为空');
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
        //加签的信息
        $arr_msg = explode(';',$add_msg);
        //$add_ids 加签的人的id数组
        foreach ($arr_msg as $key=>$value)
        {
            $a =  strstr ($value, ',', true);
            $b = explode(',', $value);
            //判断加签人中是否有已在审批进程的人
            if (in_array($a, $arr_ids)){
                $this->setError('已有加签人在审批进程中！');
                return false;
            }
            $add_ids[] = $a;
            $add_message[] = $b;  //加签人的加签信息
        }
        //判断加签人中是否有发起人
        if (in_array($petition['uid'], $add_ids)){
            $this->setError('发起人不可以为加签人！');
            return false;
        }
        //往审批人的数组中加人加签人id数组
        array_splice($arr_ids,$k,0,$add_ids);
        $new_ids = implode(',', $arr_ids);
        //更新签呈表中审批人的字段
        $qian = Petition::find()->where(['id'=>$petition_id])->one();
        $qian->ids = $new_ids;
        if ($qian->save()){
            //加签信息，遍历插入审核表中
            foreach ($add_message as $ke => $val)
            {
                $examine = new Examine();
                $examine->petition_id = $petition_id;
                $examine->uid = $val['0'];  //id
                $examine->status = 2;
                //加签的人第一个是可见  之后的是不可见  第一个是可以审核的 之后的不可以审核
                if ($ke == 0){
                    $examine->is_visible = 1;
                    $examine->flag = 1;
                }else{
                    $examine->is_visible = 2;
                    $examine->flag = 2;
                }
                $examine->add_advice = $val['1']; //加签意见
                $examine->add_time = time();
                $examine->tag = 1;  //默认不是转签来的
                $res = $examine->save();
            }
            if ($res){
                //让加签人自己的可见状态不变但是 无法审核flag=2 的时候没法审核
                $examine_people = Examine::find()
                    ->where(['uid'=>$user_id])
                    ->andWhere(['petition_id'=>$petition_id])
                    ->one();
                $examine_people->flag = 2;
                if ($examine_people->save()){
                    /* 新增推送部分 */
                    $push = $this->Jpush(current($add_ids), '签呈加签推送',3);
                    if ($push){
                        $log = $this->jpushLog(current($add_ids), '签呈加签推送');
                        if ($log){
                            return '加签成功！';
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
                    $this->setError('加签失败3！');
                    return false;
                }
            }else{
                $this->setError('加签失败2！');
                return false;
            }
        }else{
            $this->setError('加签失败1！');
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