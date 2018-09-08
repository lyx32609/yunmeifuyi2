<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use yii\data\Pagination;
use app\models\UserLocation;
class LocationListService extends Service
{
    /**
     * 员工定位查询
     * @param string $staffId 查询人
     * @param string $start 查询开始日期
     * @param string $end 查询结束日期
     * @return array
     */
    public function getLocationList($staffId,$start,$end,$page)
    {
        $data = $this->locationList($staffId,$start,$end,$page);
        return $data;
    }
  
    /**
     * 员工定位查询
     * @param string $staffId 查询人
     * @param string $start 查询开始日期
     * @param string $end 查询结束日期
     * @return array
     */
    public function locationList($staffId,$start,$end,$page)
    {
        $people = User::find()
        ->andWhere('id = :user',[':user'=>$staffId])
        ->one();
        if(!$people)
        {
            $this->setError('员工不存在!');
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
          $result = $this->getLists($people['username'],$start,$end,$page);
      //    var_dump($result);exit;
          if(!$result)
          {
              $this->setError('未获取相关信息');
              return false;
          }
          $list = array();
          for ($i = 0; $i < count($result['list']); $i++) {
            $list[$i]['locationdate'] = date('Y-m-d H:i:s',$result['list'][$i]['time']);
            $list[$i]['shopName'] = $result['list'][$i]['name']?$result['list'][$i]['name']:'';
            $list[$i]['shopLongitude'] = $result['list'][$i]['shops']['longitude']?$result['list'][$i]['shops']['longitude']:'';
            $list[$i]['shopLatitude'] = $result['list'][$i]['shops']['latitude']?$result['list'][$i]['shops']['latitude']:'';
            $list[$i]['longitude'] = $result['list'][$i]['longitude']?$result['list'][$i]['longitude']:'';
            $list[$i]['latitude'] = $result['list'][$i]['latitude']?$result['list'][$i]['latitude']:'';
            if ($result['list'][$i]['shop_id']!=0){
                $shop_longitude = sprintf("%.3f",substr(sprintf("%.4f", $result['list'][$i]['shops']['longitude']), 0,-1));
                $shop_latitude =  sprintf("%.3f",substr(sprintf("%.4f", $result['list'][$i]['shops']['latitude']), 0,-1));
                $res_longitude = sprintf("%.3f",substr(sprintf("%.4f", $result['list'][$i]['longitude']), 0,-1));
                $res_latitude = sprintf("%.3f",substr(sprintf("%.4f", $result['list'][$i]['latitude']), 0,-1));
                if ((abs($shop_longitude-$res_longitude))>0||(abs($shop_latitude-$res_latitude))>0){
                    $list[$i]['isOk'] = '不合理';
                }else {
                    $list[$i]['isOk'] = '合理';
                }
            }else{
                $list[$i]['isOk'] = '';
            }
          }
          
           return  ['list'=>$list, 'pageCount'=>$result['pageCount']]; 
    }

   /**
     * 员工定位查询
     * @param string $staffId 查询人
     * @return array
     */
    public function getLists($staffId,$start,$end, $page = 1)
    {
        $query = UserLocation::find()
        ->select(['shop_id','name','time','longitude','latitude'])
//          ->with(['shops'=>function ($query){
//             $query->select(['member_id','shopname','longitude','latitude']);
//         }]) 
        ->andWhere('user =:staffId',[':staffId'=>$staffId])
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ;
        
       $pagination = new Pagination([
        'params'=>['page'=>$page],
        'defaultPageSize' => 100,
        'totalCount' => $query->count(),
    ]);
       
        $rows = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->orderBy('time asc')->all();
        $array=[];
        foreach ($rows as $row)
        {
            $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$row['shop_id']]);
            
            if($member&&$member['ret']===0){ 
                $member=$member[0];
            }else {
                return false;
            }
            $row['shops']['member_id']=$member['member_id'];
            $row['shops']['shopname']=$member['shopname'];
            $row['shops']['longitude']=$member['longitude'];
            $row['shops']['latitude']=$member['latitude'];
            $array[]=$row;
        }
        return ['list'=>$array, 'pageCount'=>$pagination->pageCount];
    }     
   
}