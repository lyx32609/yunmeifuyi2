<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19
 * Time: 16:31
 */
namespace app\services;


use app\foundation\Service;
use app\models\OrdersSum;
use app\models\Percentum;
use app\models\Record;
use app\models\User;
use app\models\Orders;

class OrderSumNewService extends Service
{
    public function getOrderNew($username, $starttime, $endtime)
    {
        $user = User::find()
            ->where(['username'=>$username])
            ->asArray()
            ->all();
        if (!$user){
            $this->setError('没有此员工！');
            return false;
        }
        if (!empty($starttime) && !empty($endtime)){
            $starttime .= " 0:0:0";
            $endtime .= " 23:59:59";
            $start_time = strtotime($starttime);
            $end_time = strtotime($endtime);
        }
        $result = Orders::find()
            ->select(['order_id','finishtime','payed','company_name as shop','money', 'percent'])
            ->where(['staff_num'=>$username])
            ->andWhere(['<>','money','0.00'])
            ->andFilterWhere(['between','finishtime',$start_time, $end_time])
            ->orderBy('finishtime desc')
            ->asArray()
            ->all();
        if (!$result) {
            $this->setError('暂无收入！');
            return false;
        }
        // 计算提成
        $sum = 0;
        foreach ($result as $key => $value)
        {
            $sum += $value['money'];
            $result[$key]['finishtime'] = date('Y-m-d H:i:s', $value['finishtime']);
            $result[$key]['percent'] = $result[$key]['percent'] . '%';
        }
        $final['list'] = $result;
        $final['sum'] = $sum;

        if (empty($starttime) && empty($endtime)){
            // 保存数据总和
            $save = $this->saveSum($sum, $username);
            if ($save){
                return $final;
            }else{
                $this->setError('数据出错！');
                return false;
            }
        }else{
            return $final;
        }
    }
    /**
     * @param $username  员工账号
     * @return bool
     */
    public function getOrder($username, $starttime, $endtime)
    {
        $user = User::find()
            ->where(['username'=>$username])
            ->asArray()
            ->all();
        if (!$user){
            $this->setError('没有此员工！');
            return false;
        }
        $is_open = Percentum::find()
            ->where(['flag'=>1])
            ->one();
        //未设置比例
        if (empty($is_open)){
            $this->setError('未设置提成比例！');
            return false;
        }
        $record = Record::find()
            ->select(['start_time','end_time','percent'])
            ->asArray()
            ->all();
        //已开启
        if ($is_open->is_open == 1){
            //无修改记录
            if (empty($record)){
                //有查询条件
                if (!empty($starttime) && !empty($endtime)){
                    $starttime .= " 0:0:0";
                    $endtime .= " 23:59:59";
                    $start_time = strtotime($starttime);
                    $end_time = strtotime($endtime);
                    if ($start_time < $is_open->open_time){
                        $start_time = $is_open->open_time;
                    }
                }else{
                    // 无查询条件
                    $start_time = $is_open->open_time;
                    $end_time = time();
                }
                $percent = $is_open->new_per;
                //调本地表数据
                $result1 = $this->getInfo($username,$start_time, $end_time, $percent);
                if (!$result1){
                    $this->setError('未查询到数据！');
                    return false;
                }
                $result1 = $this->arraySequence($result1, 'finishtime', 'SORT_DESC');
                //计算提成
                $sum = 0;
                foreach ($result1 as $key => $value)
                {
                    $sum += $value['money'];
                }
                $final['list'] = $result1;
                $final['sum'] = $sum;
                if (empty($starttime) && empty($endtime)){
                    // 保存数据总和
                    $save = $this->saveSum($sum, $username);
                    if ($save){
                        return $final;
                    }else{
                        $this->setError('数据出错！');
                        return false;
                    }
                }else{
                    return $final;
                }
            }else{   //有修改记录   1、记录中的 2、还有修改时间到现在时间
                //有查询条件
                if (!empty($starttime) && !empty($endtime)){
                    $starttime .= " 0:0:0";
                    $endtime .= " 23:59:59";
                    $start_time = strtotime($starttime);
                    $end_time = strtotime($endtime);
                    if ($start_time < $is_open->open_time){
                        $start_time = $is_open->open_time;
                    }
                    $last = $this->dealRecord($username, $start_time, $end_time, $record, $is_open);
                    return $last;
                }else{
                    //无查询条件
                    $record[] = ['start_time'=>$is_open->time,'end_time'=>time(),'percent'=>$is_open->new_per];
                    $last = $this->dealOrdersNew($record, $username);
                    return $last;
                }

            }
            //未开启提成比例中
        }else{
            //无修改记录
            if (empty($record)){
                $this->setError('未开启提成比例！');
                return false;
            }else{   //有修改记录     1、显示修改记录中时间段的订单2、显示 最后修改时间到关闭时间
                // 有查询条件
                if (!empty($starttime) && !empty($endtime)){
                    $starttime .= " 0:0:0";
                    $endtime .= " 23:59:59";
                    $start_time = strtotime($starttime);
                    $end_time = strtotime($endtime);
                    if ($start_time < $is_open->open_time){
                        $start_time = $is_open->open_time;
                    }
                    if ($end_time > $is_open->close_time){
                        $end_time = $is_open->close_time;
                    }
                    $last = $this->dealRecord($username, $start_time, $end_time, $record, $is_open);
                    return $last;
                }else{
                    // 无查询条件
                    $record[] = ['start_time'=>$is_open->time,'end_time'=>$is_open->close_time,'percent'=>$is_open->new_per];
                    $last = $this->dealOrdersNew($record, $username);
                    return $last;
                }
            }
        }
    }
    /**
     * @param $sum 金额
     * @param $username 账号
     * @return bool 成功失败
     * 保存数据的总和
     */
    public function saveSum($sum, $username)
    {
        $people = OrdersSum::find()->where(['staff_num'=>$username])->one();
        if (!$people){
            $ordersSum = new OrdersSum();
            $ordersSum->staff_num = $username;
            $ordersSum->sum = $sum;
            $ordersSum->balance = $sum;
            if ($ordersSum->save()){
                return true;
            }
        }else{
            $old_sum = $people->sum;
            $old_blance = $people->balance;
            $people->sum = $sum;
            $people->balance = ($sum - $old_sum) + $old_blance;
            if ($people->save()){
                return true;
            }
        }
    }
    /**
     * @param $username  用户名
     * @param $start_time 开始时间
     * @param $end_time  结束时间
     * @param $record    修改记录数组
     * @param $is_open   提成比例数据
     * @return mixed
     * 数据处理 带查询
     */
    public function dealRecord($username, $start_time, $end_time, $record, $is_open)
    {
        $result = Orders::find()
            ->select(['order_id','finishtime','payed','company_name'])
            ->where(['staff_num'=>$username])
            ->andFilterWhere(['between','finishtime',$start_time, $end_time])
            ->orderBy('finishtime desc')
            ->asArray()
            ->all();
        if (!$result) {
            $this->setError('未查询到数据！');
            return false;
        }
        foreach ($result as $key=> $value)
        {
            foreach ($record as $k=> $v)
            {
                if ($value['finishtime'] > $v['start_time'] && $value['finishtime'] < $v['end_time']){
                    $percent = $v['percent'];
                }elseif ($value['finishtime'] > $is_open->time){
                    $percent = $is_open->new_per;
                }
                $res[$key]['order_id'] = $value['order_id'];
                $res[$key]['finishtime'] = date('Y-m-d H:i:s', $value['finishtime']);
                $res[$key]['payed'] = $value['payed'];
                $res[$key]['shop'] = $value['company_name'];
                // $res[$key]['money'] = substr(sprintf("%.3f",$value['payed'] * ($percent/100)),0,-1);
                $res[$key]['money'] = round($value['payed'] * ($percent/100),2);
                $res[$key]['percent'] = $percent . '%';
                if ($res[$key]['money'] == '0.00'){
                    unset($res[$key]);
                }
            }
        }
        $res =  array_values($res);
        // 计算提成
        $sum = 0;
        foreach ($res as $key => $value)
        {
            $sum += $value['money'];
        }
        $final['list'] = $res;
        $final['sum'] = $sum;
        return $final;
    }
    /**
     * @param $record   修改记录
     * @param $username  用户名
     * @return bool
     * 处理数据 不带查询
     */
    public function dealOrdersNew($record, $username)
    {
        foreach ($record as $value)
        {
            //循环调数据库数据  每个时间段的比例不一样
            $res =  $this->getInfo($username,$value['start_time'], $value['end_time'], $value['percent']);
            if (!empty($res)){
                $result[] =$res;
            }
        }
        if (!empty($result)){
            // 处理数组 排序
            foreach ($result as $k => $v) {
                foreach ($v as $m => $n) {
                    $arr2[] = $n;
                }
            }
            $arr2 = $this->arraySequence($arr2, 'finishtime', 'SORT_DESC');
            // 计算提成
            $sum = 0;
            foreach ($arr2 as $key => $value)
            {
                $sum += $value['money'];
            }
            $final['list'] = $arr2;
            $final['sum'] = $sum;
            // 保存数据总和
            $save = $this->saveSum($sum, $username);
            if ($save){
                return $final;
            }else{
                $this->setError('数据出错！');
                return false;
            }
        }else{
            $this->setError('未查询到数据！');
            return false;
        }

    }
    /**
     * @param $username
     * @param $start_time
     * @param $end_time
     * @param $percent
     * @return mixed
     * 获取数据 不带查询
     */
    public function getInfo($username,$start_time, $end_time, $percent)
    {
        $result = Orders::find()
            ->select(['order_id','finishtime','payed','company_name'])
            ->where(['staff_num'=>$username])
            ->andFilterWhere(['between','finishtime',$start_time, $end_time])
            ->orderBy('finishtime desc')
            ->asArray()
            ->all();
        //循环遍历 处理提成
        foreach ($result as $key => $value)
        {
            $res[$key]['order_id'] = $value['order_id'];
            $res[$key]['finishtime'] = date('Y-m-d H:i:s', $value['finishtime']);
            $res[$key]['payed'] = $value['payed'];
            $res[$key]['shop'] = $value['company_name'];
            // $res[$key]['money'] = substr(sprintf("%.3f",$value['payed'] * ($percent/100)),0,-1);
            $res[$key]['money'] = round($value['payed'] * ($percent/100),2);
            $res[$key]['percent'] = $percent . '%';
            if ($res[$key]['money'] == '0.00'){
                unset($res[$key]);
            }
        }
        return $res;
    }
    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }
}