<?php
namespace app\services;
use app\foundation\Service;
use app\models\User;
use app\models\UserIndex;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
use app\models\Regions;
//use app\services\GetPerBusinessRankService;
use Yii;
/**
 * Created by 祁志飞
 * User: Administrator
 * Date: 2017/4/27
 * Time: 下午 14:27
 */
class GetPerBusinessRankNewService extends Service
{
    /**
     *  获取个人业务排名
     * @param  [type] $username 用户名
     * @param  [type] $type 时间类型 1昨日 2本周（不含今日） 3本月（不含今日）
     * @return [type]  array
     */
    public function getPerInfo($username,$type)
    {

        if(!$username)
        {
            $this->setError('用户名不能为空');
            return false;
        }
        
        if($type == 2){
            //本周的开始时间
            $stime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
        }elseif ($type ==3 ){
            //本月的开始时间
            $stime = mktime(0, 0 , 0,date("m"),1,date("Y"));
        }else{
            //昨日的开始时间与结束时间戳
            $stime = strtotime('yesterday 00:00:00');
        }    
        //结束时间        
        $etime = strtotime('yesterday 23:59:59');

        //判断当前员工是否有统计记录
        $userindex = UserIndex::find()
                    ->where(['userid'=>$username])
                    ->andWhere(['between','inputtime',$stime,$etime])
                    ->count();
                    
        if($userindex != 0){
            //查询出员工的统计数据
            $department = UserIndex::find()
            ->select('userid,SUM(visitingnum) as visitingnum,SUM(registernum) as registernum , SUM(ordernum) as ordernum ,SUM(orderamount) as orderamount ,SUM(orderuser) as orderuser,SUM(deposit) as deposit , SUM(maimaijinorder) as maimaijinorder ,SUM(maimaijinamount) as maimaijinamount ,SUM(maimaijinuser) as maimaijinuser')
            ->where(['between','inputtime',$stime,$etime])
            ->groupBy('userid')
            ->orderBy('userid asc')
            ->asArray()
            ->all();
            if(count($department) >0){
                $rs_data[] = $this->getStatistical($department,$username,'visitingnum','拜访客户');
                $rs_data[] = $this->getStatistical($department,$username,'registernum','累计注册量');
                $rs_data[] = $this->getStatistical($department,$username,'registernum','累计自己注册');
                $rs_data[] = $this->getStatistical($department,$username,'ordernum','累计订单数量');
                $rs_data[] = $this->getStatistical($department,$username,'orderamount','累计订单金额');
                $rs_data[] = $this->getStatistical($department,$username,'orderuser','累计订单用户数量');
                $rs_data[] = $this->getStatistical($department,$username,'deposit','累计预存款订金额');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinorder','累计买买金订单量');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinamount','累计买买金订单金额');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinuser','累计买买金订单用户量');
            }
        }else{
            //统计所有的员工
            $countNum = UserIndex::find()
            ->groupBy('userid')
            ->count();
            $rs_data = $this->getStatisticalZero($countNum);
            
        }
        return $rs_data;
    }

    
    
    //二维数组排序方法
    function my_sort($arrays,$sort_key,$sort_order=SORT_DESC,$sort_type=SORT_NUMERIC  )
    {
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        foreach ($arrays as $k=>&$v){
            if($k != 0){
                if($v[$sort_key] == $arrays[$k-1][$sort_key] ){
                    $v['rank'] = $arrays[$k-1]['rank'];
                }else{
                    $v['rank'] = $k+1;
                }   
            }else{
                $v['rank'] = $k+1;
            }
        }
        
      //  print_r($arrays);exit();
        return $arrays;
    }
    
    
    
    //计算统计排行
    public function getStatistical($arr,$username,$index,$indexName){
       //获取客户排名
        $return_data = $this->my_sort($arr, $index);
        foreach($return_data as $k=>$v)
        {
            if($v['userid'] == $username)
            {
                //$rank = $k + 1;
                $rank = $v['rank'];
                $rs_data['rank'] = "$rank";
                $rs_data['num'] = "$v[$index]";
                $rs_data['typeName'] = $indexName;
            }
        }
        return $rs_data;
    }
    
    //返回为0的统计数组
    public function getStatisticalZero($countNum){
        $rsdata = Array
                (
                Array
                     (
                         'rank' => "1",
                         'num' => '0',
                         'typeName' => '拜访客户',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计注册量',
                     ),
                
                       Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计自己注册',
                     ),
                        Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计订单数量',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' => '0',
                         'typeName' => '累计订单金额',
                     ),
                
                        Array
                     (
                         'rank' =>"1",
                         'num' => '0',
                         'typeName' => '累计订单用户数量',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计预存款订金额',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计买买金订单量',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' => '0',
                         'typeName' => '累计买买金订单金额',
                     ),
                
                        Array
                     (
                         'rank' => "1",
                         'num' =>  '0',
                         'typeName' => '累计买买金订单用户量',
                     )
                
                );
        return $rsdata;
    }
    
    
    

}