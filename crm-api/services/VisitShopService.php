<?php
namespace app\services;

use app\foundation\Service;
use app\models\Member;
// use benben\helpers\DateHelper;
// use official\models\VisitedShop;
// use app\services\StaffService;
use app\models\User;
use app\models\ShopNote;
class VisitShopService extends Service
{
    /**
     * 指定日期内的到访店铺集合
     * @param string $staffId 查询人
     * @param string $start 查询开始日期
     * @param string $end 查询结束日期
     * @return array
     */
    public function getVisitShop($staffId,$start,$end)
    {
        $data = $this->visitShop($staffId,$start,$end);
        return $data;
    }
  
   /**
     *指定日期内的到访店铺集合
     * @param string $staffId 查询人
     * @param string $date 查询日期
     * @return array
     */
    public function visitShop($staffId,$start,$end)
    {
        $people = User::find()
                  ->andWhere('id = :user',[':user'=>$staffId])
                  ->one();
         if(!$people)
            {
                $this->setError('此人不存在');
                return false;
            }
          $start = strtotime($start);
          $end = strtotime($end);
          $start = $start ? $start : 0;
          $end = $end ? $end : strtotime(date('Y-m-d',time()));
          
          if($start && $end)
          {
              $end += 86400;
          }
          else{
              $start = strtotime(date('Y-m-d',time()));
              $end = $start+86400;
          }
          $staffId = $people['username'];
          $myShops =\Yii::$app->api->request('basic/getMemberByStaff',['staffId'=>$staffId]);
          if($myShops&&$myShops['ret']===0)
          {
              $myShops=$myShops['result'];
          }else{
              $this->setError('未能获取到绑定店铺');
              return false;
          }
          $visitedShopRows = $this->getVisitShops($staffId,$start,$end);
          if($visitedShopRows===false)
          {
              $this->setError($this->getError());
              return false;
          }
          $visitedShops = [];
          foreach ($visitedShopRows as $item)
          {
              $visitedShops[$item['shop_id']] = $item;
          }
          
          $result = [];
          
          foreach ($myShops as $i=>$item)
          {
              $result[$i] = [
                  'shopId' => $item['member_id'],
                  'shopName' => $item['shopname'],
                  'longitude' => $item['longitude'],
                  'latitude' => $item['latitude'],
                  'mine' => 1,
                  'isVisit' => 0,
                  'visitDate' => 0
              ];
              
              if(array_key_exists($item['member_id'], $visitedShops))
              {
                  $result[$i]['isVisit'] = 1;
                  $result[$i]['visitDate'] = date('Y-m-d H:i:s', $visitedShops[$item['member_id']]['time']);
                  unset($visitedShops[$item['member_id']]);
              }
          }
          
          foreach ($visitedShops as $item)
          {
              $result[] = [
                  'shopId' => $item['shop_id'],
                  'shopName' => $item['shops']['shopname'],
                  'longitude' => $item['shops']['longitude'],
                  'latitude' => $item['shops']['latitude'],
                  'mine' => 0,
                  'isVisit' => 1,
                  'visitDate' => date('Y-m-d H:i:s', $item['time']),
              ];
          }
          
          return  $result; 
    }

    /**
     * 业务员绑定访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
//     public function getShops($staffId)
//     {
//         $member=\Yii::$app->api->request('basic/getMemberByStafff',['staffId'=>$staffId]);
//         return $member;
//     }     
    /**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param int mid 查询商店id
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    public function getVisitShops($staffId,$start,$end)
    {
        $rows = ShopNote::find()
        ->select(['shop_id','MAX(time) as time','longitude','latitude']) 
        ->andWhere('user =:staffId',[':staffId'=>$staffId])
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->asArray()->groupBy('shop_id')->orderBy('time desc')->all();
        $arr=[];
        if(empty($rows))
        {
            return $rows;
        }else{
        foreach ($rows as $row)
        {
            $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$row['shop_id']]);
            
            if($member['ret']==0)
            {
                $row['shops']=$member[0];
            }else{
                 $this->setError('member_id:'.$row['shop_id'].'  错误提示：'.$member['msg']);
                 return false;
            }
            $arr[]=$row;
        }
        return $arr;
        }
    }
   
}