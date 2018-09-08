<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserLocation;
use app\models\Member;
use app\models\Supplier;
use app\benben\DateHelper;

class LocationListNewService extends Service
{
    /**
     * 员工定位查询
     * @param string $staffId 查询人
     * @param string $start 查询开始日期
     * @param string $end 查询结束日期
     * @return array
     */
    public function getLocationList($staffId,$start,$end,$offset,$limit,$type)
    {
      if($type == '1'){
          $start = date('Y-m-d', DateHelper::getTodayStartTime());
          $end = date('Y-m-d', DateHelper::getTodayEndTime());
      }
      if($type == '2'){
          $start = date('Y-m-d', DateHelper::getWeekStartTime(0));
          $end = date('Y-m-d', DateHelper::getThisWeekEndTime(0));
      }
      if($type == '3'){
        $start = date('Y-m-d', DateHelper::getMonthStartTime(0));
        $end = date('Y-m-d', DateHelper::getMonthEndTime(0));
      }
        $data = $this->locationList($staffId,$start,$end,$offset,$limit);
        return $data;
    }
  
    /**
     * 员工定位查询
     * @param string $staffId 查询人
     * @param string $start 查询开始日期
     * @param string $end 查询结束日期
     * @return array
     */
    public function locationList($staffId,$start,$end,$offset,$limit)
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
          $result = $this->getNewLists($people['username'],$start,$end,$offset,$limit);

          $list = array();
          for ($i = 0; $i < count($result['list']); $i++) {
            $list[$i]['locationdate'] = date('Y-m-d H:i:s',$result['list'][$i]['time']);
            if($result['list'][$i]['belong'] == 1){
              $list[$i]['shopName'] = $result['list'][$i]['shops']['shopname']?$result['list'][$i]['shops']['shopname']:'';
            } else {
              $list[$i]['shopName'] = $result['list'][$i]['shops']['company_name']?$result['list'][$i]['shops']['company_name']:'';
            }
            $list[$i]['shopLongitude'] = $result['list'][$i]['shops']['longitude']?$result['list'][$i]['shops']['longitude']:'';
            $list[$i]['shopLatitude'] = $result['list'][$i]['shops']['latitude']?$result['list'][$i]['shops']['latitude']:'';
            $list[$i]['longitude'] = $result['list'][$i]['longitude']?$result['list'][$i]['longitude']:'';
            $list[$i]['latitude'] = $result['list'][$i]['latitude']?$result['list'][$i]['latitude']:'';
            $list[$i]['shop_ip'] = $result['list'][$i]['shop_id']?$result['list'][$i]['shop_id']:'';
            $list[$i]['belong'] = $result['list'][$i]['belong']?$result['list'][$i]['belong']:'';
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
    public function getLists($staffId,$start,$end,$offset,$limit)
    {

        $query = UserLocation::find()
        ->select(['shop_id','name','time','longitude','latitude'])
         ->with(['shops'=>function ($query){
            $query->select(['member_id','shopname','longitude','latitude']);
          }]) 
        ->andWhere('user =:staffId',[':staffId'=>$staffId])
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ;
        
  /*      $pagination = new Pagination([
        'params'=>['page'=>$page],
        'defaultPageSize' => 100,
        'totalCount' => $query->count(),
    ]); */
       
        $rows = $query->offset($offset)
                ->limit($limit)
                ->asArray()->orderBy('time desc')->all();
        return ['list'=>$rows, 'pageCount'=>Count($rows)];
    }     
    private function getNewLists($user,$start,$end,$offset,$limit)
    {
        $rows = UserLocation::find()
        ->select(['shop_id','name','time','longitude','latitude','belong'])
        ->andWhere('user =:user',[':user'=>$user])
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->andWhere('belong != :belong ', [':belong' => '0'])
        ->andWhere(['type' => '0'])
        ->offset($offset)
        ->limit($limit)
        ->asArray()->orderBy('time desc')->all();
        for($i = 0; $i < count($rows); $i++){
          if($rows[$i]['belong'] == 1){ 
                $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
                if($member['ret']===0)
                {
                    $rows[$i]['shops']=$member[0];
                }else {
                    $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
                    $rows[$i]['shops']['longitude']=0;
                    $rows[$i]['shops']['latitude']=0;
                }
            }else  if($rows[$i]['belong'] == 2){
                $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
              if($supplier['ret']===0)
                {
                  $rows[$i]['shops']=$supplier[0];
                }else{
                    $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
                    $rows[$i]['shops']['longitude']=0;
                    $rows[$i]['shops']['latitude']=0;
                }
                /*  $row['shops']=Supplier::find()
                    ->select(['uid','company_name as shopname','longitude','latitude'])
                    ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
                    ->asArray()->one(); */
            } else if($rows[$i]['belong'] == 0){
              if($rows[$i]['belong'] == 1){ 
                  $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
                  if($member['ret']===0)
                  {
                      $rows[$i]['shops']=$member[0];
                  }else {
                      $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
                      $rows[$i]['shops']['longitude']=0;
                      $rows[$i]['shops']['latitude']=0;
                  }
              }else  if($rows[$i]['belong'] == 2){
                  $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
                if($supplier['ret']===0)
                  {
                    $rows[$i]['shops']=$supplier[0];
                  }else{
                      $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
                      $rows[$i]['shops']['longitude']=0;
                      $rows[$i]['shops']['latitude']=0;
                  }
                  /*  $row['shops']=Supplier::find()
                      ->select(['uid','company_name as shopname','longitude','latitude'])
                      ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
                      ->asArray()->one(); */
              }
            }
           
        } 
        return ['list'=>$rows, 'pageCount'=>Count($rows)];
    }
}