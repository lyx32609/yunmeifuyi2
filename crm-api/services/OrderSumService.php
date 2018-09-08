<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19
 * Time: 16:31
 */
namespace app\services;


use app\foundation\Service;
use app\models\Percentum;
use app\models\Record;
use app\models\User;

class OrderSumService extends Service
{
    /**
     * @param $username  员工账号
     * @return bool
     */
    public function getOrder($username)
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
        $record = Record::find()->select(['start_time','end_time','percent'])->asArray()->all();
        //已开启
        if ($is_open->is_open == 1){
            //无修改记录
            if (empty($record)){
                $start_time = $is_open->open_time;
                $end_time = time();
                $percent = $is_open->new_per;
                //调集采订单
                $result1 = $this->countSum($username,$start_time, $end_time, $percent);
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
                return $final;
            }else{     //有修改记录   1、记录中的 2、还有修改时间到现在时间
                $record[] = ['start_time'=>$is_open->time,'end_time'=>time(),'percent'=>$is_open->new_per];
                $last = $this->dealOrders($record, $username);
                return $last;
            }
            //未开启提成比例中
        }else{
            //无修改记录
            if (empty($record)){
                $this->setError('未开启提成比例！');
                return false;
            }else{
                //有修改记录 1、显示修改记录中时间段的订单2、显示 最后修改时间到关闭时间
                $record[] = ['start_time'=>$is_open->time,'end_time'=>$is_open->close_time,'percent'=>$is_open->new_per];
                $last = $this->dealOrders($record, $username);
                return $last;
            }
        }
    }
    /**
     * @param $record 记录
     * @param $username 员工工号
     * @return bool
     */
    public function dealOrders($record,$username)
    {
        foreach ($record as $value)
        {
            //循环调集采订单  每个时间段的比例不一样
            $res =  $this->countSum($username,$value['start_time'], $value['end_time'], $value['percent']);
            if (!empty($res)){
                $result[] =$res;
            }
        }
        if (!empty($result)){
            // 处理数组
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
            return $final;
        }else{
            $this->setError('未查询到数据！');
            return false;
        }
    }
    /**
     * @param $username   员工的账号
     * @param $start_time 开始时间
     * @param $endtime    结束时间
     * @param $percent    提成比例
     * @return bool
     */
    public function countSum($username, $start_time, $endtime, $percent)
    {
        $data['staff'] = $username;
        $data['start_time'] = $start_time;
        $data['end_time'] = $endtime;
        // 调集采接口 获取员工订单
        $result = \Yii::$app->api->request('order/staffOrders', $data);

        if ($result['ret'] != 0) {
            return [];
        }else{
            $order = $result['data'];
            //循环遍历 处理提成
            foreach ($order as $key => $value)
            {
                $res[$key]['order_id'] = $value['order_id'];
                $res[$key]['finishtime'] = date('Y-m-d H:i:s', $value['finishtime']);
                $res[$key]['payed'] = $value['payed'];
                $res[$key]['money'] = substr(sprintf("%.3f",$value['payed'] * ($percent/100)),0,-1);
                $res[$key]['percent'] = $percent . '%';
                if ( $res[$key]['money'] == '0.00'){
                    unset($res[$key]);
                }
            }
            return $res;
        }
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