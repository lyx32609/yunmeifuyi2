<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 13:12
 */
namespace app\services;

use app\foundation\Service;
use app\models\BindCount;
use app\models\OrdersSum;
use app\models\WithdRate;
use app\models\WithdrawRecord;
use yii\data\Pagination;
class OrderRateService extends Service
{
    /**
     * @param $username
     * @param $money
     * @return mixed
     * 转账到买买金 接口
     */
    public function Withdraw($username,$money)
    {
        if (!$username) {
            $this->setError('用户账号不能为空');
            return false;
        }
        if (!$money) {
            $this->setError('转账金额不可为空');
            return false;
        }
        //查询 转账金额 是否 大于 可转金额
        $balance = OrdersSum::find()
            ->where(['staff_num'=>$username])
            ->one();
        if (!$balance){
            $this->setError('查无记录！');
            return false;
        }
        if ($money > $balance['pay_order_sum']){
            $this->setError('金额超出限额！');
            return false;
        }
        //查询账号绑定买买金的 id参数
        $partner_login_id = BindCount::find()
            ->where(['local_count'=>$username])
            ->andFilterWhere(['other_department'=>12])//买买金部门
            ->asArray()
            ->one();
        if (!$partner_login_id){
            $this->setError('账号未关联买买金！');
            return false;
        }
        $parms_id =  $partner_login_id['other_count'];

        //手续费的计算
        $rate = WithdRate::find()->one();
        if ($rate === null){
            $charges = 0;
        }
        //未开启手续费为2
        if ($rate->is_open == 2){
            $charges = 0;
        }elseif($rate->is_open == 1){
            //单笔手续费
            if ($rate->is_open_which == 'money'){
                $charges = $rate->pound_money;
                //百分比手续费
            }elseif($rate->is_open_which == 'percent'){
                $charges = $money * ($rate->pound_percent/100);
            }
        }
        $rand = $this->createNum();
        $data['partner_login_id'] = $parms_id; //买买金账号绑定
        $data['total_money'] = $money;  //转账金额
        $data['service_fee'] = $charges; //手续费
        $data['trade_code'] = '4050';  //'4050'=>云管理个人账户转账，买买金交易码
        $data['order_id'] = $rand;  //订单流水号

        //买买金接口调用
        $result = \Yii::$app->mmj_api->mmj_request('mars/masterAccountTransferredMember', $data);

        $result = $result['response'];
        if ($result['status_code'] != '00'){
            $this->setError($result['ret_message']);
            return false;
        }else{
            //减余额 和 财务支付的钱数
            $balance->updateCounters(['balance'=>-$money]);
            $balance->updateCounters(['pay_order_sum'=>-$money]);
            $trans = new WithdrawRecord();
            $trans->money = $money;   // 转账的金额
            $trans->service_fee = $charges; //收取的手续费
            $trans->time = time();
            $trans->status = 1;    //转账成功
            $trans->staff_num = $username; //账号
            $trans->flag = 1;   //1是转账
            $trans->order_id = $rand; // 随机生成的订单号
            if ($trans->save()){
                return '已成功转出到买买金';
            }else{
                $this->setError('转账成功，添加记录失败！');
                return false;
            }
        }
    }

    /**
     * @param $username
     * @param $money
     * @param $password
     * 提现买买金余额到银行卡接口
     */
    public function Tixian($username, $money, $password)
    {
        if (!$username) {
            $this->setError('用户账号不能为空');
            return false;
        }
        if (!$money) {
            $this->setError('提现金额不可为空');
            return false;
        }
        if (!$password) {
            $this->setError('密码不可为空');
            return false;
        }
        //查询账号绑定买买金的 id参数
        $partner_login_id = BindCount::find()
            ->where(['local_count'=>$username])
            ->andFilterWhere(['other_department'=>12])//买买金部门
            ->asArray()
            ->one();
        if (!$partner_login_id){
            $this->setError('账号未关联买买金！');
            return false;
        }
        $parms_id =  $partner_login_id['other_count'];
        $rand1 = $this->createNum();
        //获取提现手续费
        $service_fee = $this->getFee($parms_id, $money, $rand1);

        if ($service_fee['ret'] == 100){
            $this->setError($service_fee['result']);
            return false;
        }
        $data['partner_login_id'] = $parms_id;  //买买金参数
        $data['trade_money'] = $money;     //提现金额
        $data['serial_no'] = $rand1;       //生成流水号
        $data['service_fee'] = $service_fee['service_fee'];  //银行生成的手续费
        $data['pay_pwd'] = $password;  //密码
        $data['trade_code'] = '4053';

        $result = \Yii::$app->mmj_api->mmj_request('rjt/allWithdraw', $data);

        $result = $result['response'];

        if ($result['status_code'] != '0000'){
            $this->setError($result['ret_message']);
            return false;
        }else{
            $trans = new WithdrawRecord();
            $trans->money = $money;   // 提现的金额
            $trans->service_fee = $service_fee['service_fee']; //收取的手续费
            $trans->time = time();
            $trans->status = 1;    //成功
            $trans->staff_num = $username; //账号
            $trans->flag = 2;   //2是提现
            $trans->order_id = $rand1; // 随机生成的订单号
            if ($trans->save()){
                return '已成功转出到银行卡';
            }else{
                $this->setError('提现成功，添加记录失败！');
                return false;
            }
        }
    }

    /**
     * @param $parms_id
     * @param $money
     * @param $rand1
     * @return bool
     * 获取银行提现 手续费 调买买金接口获取
     */
    public function getFee($parms_id, $money, $rand1)
    {
        $data['partner_login_id'] = $parms_id;  // 买买金参数
        $data['trade_money'] = $money;   //提现金额
        $data['serial_no'] = $rand1;   //随机流水号
        $data['trade_code'] = '4052';

        $result = \Yii::$app->mmj_api->mmj_request('rjt/allWithdrawServiceFeeTrial', $data);

        $result = $result['response'];

        if ($result['status_code'] == '0000'){
            return ['ret'=>0,'service_fee'=>$result['service_fee']];
        }elseif ($result['status_code'] == '6000'){ // 6000 用户绑定的是华夏银行手续费为0
            return ['ret'=>0,'service_fee'=>0];
        }else{
            return ['ret'=>100,'result'=>$result['ret_message']];
        }
    }
    /**
     * @return string
     * 生成随机的流水订单号
     */
    public function createNum(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    /**
     * @param $username
     * @return mixed
     * 获取账号余额
     */
    public function getBalance($username)
    {
        if (!$username) {
            $this->setError('用户账号不能为空');
            return false;
        }
        $balance = OrdersSum::find()
            ->where(['staff_num'=>$username])
            ->one();
        if (!$balance) {
            $this->setError('暂无收入！');
            return false;
        }
        //手续费显示
        $rate = WithdRate::find()->one();
        if ($rate === null){
            $charges = '未设置费率！';
        }
        //未开启手续费
        if ($rate->is_open == 2){
            $charges = '无';
        }elseif ($rate->is_open == 1){
            //单笔手续费
            if ($rate->is_open_which == 'money'){
                $charges = '每笔' . $rate->pound_money . '元';
                //百分比手续费
            }elseif($rate->is_open_which == 'percent'){
                $charges = '提现金额的' . $rate->pound_percent . '%';
            }
        }
        //查询账号绑定买买金的 id参数
        $partner_login_id = BindCount::find()
            ->where(['local_count'=>$username])
            ->andFilterWhere(['other_department'=>12])//买买金部门
            ->asArray()
            ->one();
        if (!$partner_login_id){
            $flag = 1;    //未绑定买买金账号
        }else{
            $flag = 0;    //绑定买买金账号
        }
        $res['balance'] = $balance['balance'];
        $res['pay_order_sum'] = $balance['pay_order_sum'];
        $res['charges'] = $charges;
        $res['flag'] = $flag;
        return $res;
    }

    /**
     * @param $username
     * 获取买买金账户余额接口
     */
    public function getMmjBalance($username)
    {
        if (!$username) {
            $this->setError('用户账号不能为空');
            return false;
        }
        //查询账号绑定买买金的 id参数
        $partner_login_id = BindCount::find()
            ->where(['local_count'=>$username])
            ->andFilterWhere(['other_department'=>12])//买买金部门
            ->asArray()
            ->one();
        if (!$partner_login_id){
            $this->setError('账号未关联买买金！');
            return false;
        }
        $parms_id =  $partner_login_id['other_count'];
        $data['partner_login_id'] = $parms_id;

        $result = \Yii::$app->mmj_api->mmj_request('mars/queryMemberBalance', $data);

        $result = $result['response'];

        if ($result['status_code'] != '0000'){
            $this->setError($result['ret_message']);
            return false;
        }
        //买买金账户余额查询 显示 balance
        $res = $result['data'];
        return ['mmj_balance'=>$res['balance']] ;
    }

    /**
     * @param $username
     * @return array|\yii\db\ActiveRecord[]
     * 查询提现记录
     */
    public function withDrawRecord($username, $starttime, $endtime, $page_count, $page_size)
    {
        if (!$username) {
            $this->setError('用户账号不能为空！');
            return false;
        }
        if (!$page_count) {
            $this->setError('分页参数不能为空！');
            return false;
        }
        if (!$page_size) {
            $this->setError('分页参数不能为空！');
            return false;
        }
        if (!empty($starttime) && !empty($endtime)){
            $starttime .= " 0:0:0";
            $endtime .= " 23:59:59";
            $starttime = strtotime($starttime);
            $endtime = strtotime($endtime);
        }
        $query  = WithdrawRecord::find()
            ->select(['time','money','status'])
            ->where(['staff_num'=>$username])
            ->andFilterWhere(['flag'=>2])   //提现记录
            ->andFilterWhere(['between','time',$starttime, $endtime])
            ->orderBy('time desc');
        //分页参数
        $pages = new Pagination([
            'params'=>['page'=>$page_count],
            'defaultPageSize' => $page_size,
            'totalCount' => $query->count(),
        ]);
        $total_page = ceil($query->count()/$page_size);

        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        if (empty($model)){
            $this->setError('暂无数据');
            return false;
        }
        foreach ($model as $key=>$item) {
            $model[$key]['time'] = date('Y-m-d H:i:s', $item['time']);
        }
        return ['list'=>$model,'total_page'=>$total_page];
    }
}