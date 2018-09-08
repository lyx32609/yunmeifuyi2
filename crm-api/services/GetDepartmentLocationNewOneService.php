<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\UserGroup;
use app\models\UserLocation;
use app\benben\DateHelper;
use app\models\User;
use app\models\UserBusiness;

class GetDepartmentLocationNewOneService extends Service
{
	/**
	 * 定位数据导出
	 * @param  [type] $department_id [部门id]
	 * @param  [type] $user_id       [用户id]
	 * @param  [type] $type          [1本日 2本周 3本月]
	 * @return [type]                [description]
	 */
	public function getDepartmentLocation($department_id, $user_id, $type)
	{
		if($type == '1'){
			$start = DateHelper::getTodayStartTime();
        	$end = DateHelper::getTodayEndTime();
		}
		if($type == '2'){
			$start = DateHelper::getWeekStartTime(0);
			$end = DateHelper::getThisWeekEndTime(0);
		}
		if($type == '3'){
			$start = DateHelper::getMonthStartTime(0);
			$end = DateHelper::getMonthEndTime(0);
		}
		if($department_id) {
			$department = UserDepartment::find()
					->select(['domain_id'])
					->where(['id' => $department_id])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			if(!$department){
				$this->setError('部门不存在');
				return false;
			}
			$group = UserGroup::find()
					->select(['id'])
					->where(['department_id' => $department_id])
					->asArray()
					->all();
			if(!$group){
				$user = User::find()
						->select(['username'])
						->where(['department_id' => $department_id])
						->andWhere(['domain_id' => $department['id']])
						->asArray()
						->all();
				
			} else {
				for($i = 0; $i < count($group); $i++){
					$group_id[$i] = $group[$i]['id'];
				}
				$user = User::find()
					->select(['username'])
					->where(['department_id' => $department_id])
					->andWhere(['domain_id' => $department['domain_id']])
					->andWhere(['in', 'group_id', $group_id])
					->asArray()
					->all();
			}
			if(!$user){
				$this->setError(['无用户数据']);
				return false;
			}
			for($i = 0; $i < count($user); $i++){
					$list[$i] = $user[$i]['username'];
			}
			$list = $this->getLocation($list, $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		if($user_id) {
			$user = User::find()
					->select(['username'])
					->where(['id' => $user_id])
					->asArray()
					->one();
			if(!$user){
				$this->setError('用户不存在');
				return false;
			}
			$list = $this->getLocation($user['username'], $start, $end);
			if(!$list){
				$this->setError('暂无数据');
				return false;
			}
		}
		return $list;
	}
	
	/**
	 * 查询员工业务定位记录
	 * @param  [type] $user  [用户名]
	 * @param  [type] $start [开始时间]
	 * @param  [type] $end   [结束时间]
	 * @return [type]        [description]
	 */
	public function getLocation($user, $start, $end)
	{
		if(is_array($user)){
			$list = $this->getArray($user, $start, $end);
		}
		if(is_string($user)){
			$list = $this->getString($user, $start, $end);
		}
		if(!$list){
			return false;
		}
    return $list;
    }

	/**
	 * 查询员工信息
	 * @param  [type] $user [description]
	 * @return [type]       [description]
	 */
	public function selectUser($user)
	{
		$data = User::find()
				->select(['name'])
				->where(['username' => $user])
				->asArray()
				->one();
		return $data;
	}
	/**
	 * 查询定位记录
	 * @param  [type] $user   [description]
	 * @param  [type] $start  [description]
	 * @param  [type] $end    [description]
	 * @param  [type] $offset [description]
	 * @param  [type] $limit  [description]
	 * @return [type]         [description]
	 */
	private function getArray($user,$start,$end)
    {
        $rows = UserLocation::find()
            ->select(['shop_id','name','user', 'time','longitude','latitude','belong', 'type', 'reasonable', 'username'])
            ->where(['in', 'user',$user])
            //->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
            ->andWhere(['between', 'time' , $start, $end])
            ->andWhere(['type' => 0])
            ->asArray()
            ->orderBy('time desc')
            ->all();
        // for($i = 0; $i < count($rows); $i++){
        //   if($rows[$i]['belong'] == 1){ 
        //         $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
        //         if($member['ret']===0)
        //         {
        //             $rows[$i]['shops']=$member[0];
        //         }else {
        //             $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
        //             $rows[$i]['shops']['longitude']=0;
        //             $rows[$i]['shops']['latitude']=0;
        //         }
        //     }else  if($rows[$i]['belong'] == 2){
        //         $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
        //       if($supplier['ret']===0)
        //         {
        //           $rows[$i]['shops']=$supplier[0];
        //         }else{
        //             $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
        //             $rows[$i]['shops']['longitude']=0;
        //             $rows[$i]['shops']['latitude']=0;
        //         }
        //         /*  $row['shops']=Supplier::find()
        //             ->select(['uid','company_name as shopname','longitude','latitude'])
        //             ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
        //             ->asArray()->one(); */
        //     } else if($rows[$i]['belong'] == 0){
        //       if($rows[$i]['belong'] == 1){ 
        //           $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
        //           if($member['ret']===0)
        //           {
        //               $rows[$i]['shops']=$member[0];
        //           }else {
        //               $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
        //               $rows[$i]['shops']['longitude']=0;
        //               $rows[$i]['shops']['latitude']=0;
        //           }
        //       }else  if($rows[$i]['belong'] == 2){
        //           $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
        //         if($supplier['ret']===0)
        //           {
        //             $rows[$i]['shops']=$supplier[0];
        //           }else{
        //               $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
        //               $rows[$i]['shops']['longitude']=0;
        //               $rows[$i]['shops']['latitude']=0;
        //           }
        //           /*  $row['shops']=Supplier::find()
        //               ->select(['uid','company_name as shopname','longitude','latitude'])
        //               ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
        //               ->asArray()->one(); */
        //       }
        //     }
           
        // } 
        if($rows){
          for($i = 0; $i < count($rows); $i++){
              if(!$rows[$i]['reasonable']){
                $rows[$i]['reasonable'] = '';
              }
              if(!$rows[$i]['username']){
                $rows[$i]['username'] = '';
              }
          }
          $result = UserBusiness::find()
                ->select(['id','customer_name','staff_num', 'time', 'customer_longitude', 'customer_latitude','staff_num'])
                ->where(['in', 'staff_num',$user])
                ->andWhere(['between', 'time' , $start, $end])
                ->asArray()
                ->all();

          if($result){
            $j = count($rows);
            for($i = 0; $i < count($result); $i++){
              $rows[$j + $i]['shop_id'] = $result[$i]['id'];
              $rows[$j + $i]['name'] = $result[$i]['customer_name'];
              $rows[$j + $i]['user'] = $result[$i]['staff_num'];
              $rows[$j + $i]['time'] = $result[$i]['time'];
              $rows[$j + $i]['longitude'] = $result[$i]['customer_longitude'];
              $rows[$j + $i]['latitude'] = $result[$i]['customer_latitude'];
              $rows[$j + $i]['belong'] = '0';
              $rows[$j + $i]['type'] = '1';
              $rows[$j + $i]['reasonable'] = '';
              $rows[$j + $i]['username'] = $result[$i]['staff_num'];
            }
            $belong = UserLocation::find()
                ->select(['shop_id','name','user', 'time','longitude','latitude','belong', 'type', 'reasonable', 'username'])
                ->where(['in', 'user',$user])
                //->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
                ->andWhere(['between', 'time' , $start, $end])
                ->andWhere(['type' => 1])
                ->andWhere(['belong' => 0])
                ->asArray()
                ->orderBy('time desc')
                ->all();
            if($belong){
                for($i = 0; $i < count($belong); $i++){
                  if(!$belong[$i]['reasonable']){
                    $belong[$i]['reasonable'] = '';
                  }
                  if(!$belong[$i]['username']){
                    $belong[$i]['username'] = '';
                  }
                }
                 $z = count($rows);
              for($i = 0; $i < count($belong); $i++){
                $rows[$z + $i]['shop_id'] = $belong[$i]['shop_id'];
                $rows[$z + $i]['name'] = $belong[$i]['name'];
                $rows[$z + $i]['time'] = $belong[$i]['time'];
                $rows[$z + $i]['user'] = $belong[$i]['user'];
                $rows[$z + $i]['longitude'] = $belong[$i]['longitude'];
                $rows[$z + $i]['latitude'] = $belong[$i]['latitude'];
                $rows[$z + $i]['belong'] = $belong[$i]['belong'];
                $rows[$z + $i]['type'] = $belong[$i]['type'];
                $rows[$z + $i]['reasonable'] = $belong[$i]['reasonable'];
                $rows[$z + $i]['username'] = $belong[$i]['username'];
              }
            }else {
              return $rows;
            }
          } else {
            return $rows;
          }
          
        }else {
          return false;
        }
        $list = $this->my_sort($rows, 'time');
        return $list;
    }
    /**
     * 如果参数为字符串
     * @param  [type] $user   [description]
     * @param  [type] $start  [description]
     * @param  [type] $end    [description]
     * @param  [type] $offset [description]
     * @param  [type] $limit  [description]
     * @return [type]         [description]
     */
    private function getString($user,$start,$end)
    {
        $rows = UserLocation::find()
            ->select(['shop_id','name','user','time','longitude','latitude','belong', 'type', 'reasonable', 'username'])
            ->where('user =:user',[':user'=>$user])
            //->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
            ->andWhere(['between', 'time' , $start, $end])
            ->andWhere(['type' => 0])
            ->asArray()
            ->orderBy('time desc')
            ->all();
        // for($i = 0; $i < count($rows); $i++){
        //   if($rows[$i]['belong'] == 1){ 
        //         $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
        //         if($member['ret']===0)
        //         {
        //             $rows[$i]['shops']=$member[0];
        //         }else {
        //             $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
        //             $rows[$i]['shops']['longitude']=0;
        //             $rows[$i]['shops']['latitude']=0;
        //         }
        //     }else  if($rows[$i]['belong'] == 2){
        //         $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
        //       if($supplier['ret']===0)
        //         {
        //           $rows[$i]['shops']=$supplier[0];
        //         }else{
        //             $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
        //             $rows[$i]['shops']['longitude']=0;
        //             $rows[$i]['shops']['latitude']=0;
        //         }
        //         /*  $row['shops']=Supplier::find()
        //             ->select(['uid','company_name as shopname','longitude','latitude'])
        //             ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
        //             ->asArray()->one(); */
        //     } else if($rows[$i]['belong'] == 0){
        //       if($rows[$i]['belong'] == 1){ 
        //           $member=\Yii::$app->api->request('basic/getMember',['member_id'=>$rows[$i]['shop_id']]);
        //           if($member['ret']===0)
        //           {
        //               $rows[$i]['shops']=$member[0];
        //           }else {
        //               $rows[$i]['shops']['name']='采购商编号：'.$rows[$i]['shop_id'].'无信息';
        //               $rows[$i]['shops']['longitude']=0;
        //               $rows[$i]['shops']['latitude']=0;
        //           }
        //       }else  if($rows[$i]['belong'] == 2){
        //           $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId' => $rows[$i]['shop_id']]);
        //         if($supplier['ret']===0)
        //           {
        //             $rows[$i]['shops']=$supplier[0];
        //           }else{
        //               $rows[$i]['shops']['company_name']='供货商编号：'.$rows[$i]['shop_id'].'无信息';
        //               $rows[$i]['shops']['longitude']=0;
        //               $rows[$i]['shops']['latitude']=0;
        //           }
        //           /*  $row['shops']=Supplier::find()
        //               ->select(['uid','company_name as shopname','longitude','latitude'])
        //               ->andWhere('uid=:uid',[':uid'=>$row['shop_id']])
        //               ->asArray()->one(); */
        //       }
        //     }
           
        // } 
        if($rows){
          for($i = 0; $i < count($rows); $i++){
              if(!$rows[$i]['reasonable']){
                $rows[$i]['reasonable'] = '';
              }
              if(!$rows[$i]['username']){
                $rows[$i]['username'] = '';
              }
          }
          $result = UserBusiness::find()
                ->select(['id','customer_name','staff_num', 'time', 'customer_longitude', 'customer_latitude','staff_num'])
                ->where(['staff_num' => $user])
                ->andWhere(['between', 'time' , $start, $end])
                ->asArray()
                ->all();

          if($result){
            $j = count($rows);
            for($i = 0; $i < count($result); $i++){
              $rows[$j + $i]['shop_id'] = $result[$i]['id'];
              $rows[$j + $i]['name'] = $result[$i]['customer_name'];
              $rows[$j + $i]['time'] = $result[$i]['time'];
              $rows[$j + $i]['user'] = $result[$i]['staff_num'];
              $rows[$j + $i]['longitude'] = $result[$i]['customer_longitude'];
              $rows[$j + $i]['latitude'] = $result[$i]['customer_latitude'];
              $rows[$j + $i]['belong'] = '0';
              $rows[$j + $i]['type'] = '1';
              $rows[$j + $i]['reasonable'] = '';
              $rows[$j + $i]['username'] = $result[$i]['staff_num'];
            }
            $belong = UserLocation::find()
                ->select(['shop_id','name','user','time','longitude','latitude','belong', 'type', 'reasonable', 'username'])
                ->where('user =:user',[':user'=>$user])
                //->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
                ->andWhere(['between', 'time' , $start, $end])
                ->andWhere(['type' => 1])
                ->andWhere(['belong' => 0])
                ->asArray()
                ->orderBy('time desc')
                ->all();
            if($belong){
                for($i = 0; $i < count($belong); $i++){
                  if(!$belong[$i]['reasonable']){
                    $belong[$i]['reasonable'] = '';
                  }
                  if(!$belong[$i]['username']){
                    $belong[$i]['username'] = '';
                  }
                }
                 $z = count($rows);
              for($i = 0; $i < count($belong); $i++){
                $rows[$z + $i]['shop_id'] = $belong[$i]['shop_id'];
                $rows[$z + $i]['name'] = $belong[$i]['name'];
                $rows[$z + $i]['time'] = $belong[$i]['time'];
                $rows[$z + $i]['user'] = $belong[$i]['user'];
                $rows[$z + $i]['longitude'] = $belong[$i]['longitude'];
                $rows[$z + $i]['latitude'] = $belong[$i]['latitude'];
                $rows[$z + $i]['belong'] = $belong[$i]['belong'];
                $rows[$z + $i]['type'] = $belong[$i]['type'];
                $rows[$z + $i]['reasonable'] = $belong[$i]['reasonable'];
                $rows[$z + $i]['username'] = $belong[$i]['username'];
              }
            }else {
              return $rows;
            }
          } else {
            return $rows;
          }
          
        }else {
          return false;
        }
        $list = $this->my_sort($rows, 'time');

        return $list;
    }
      //二维数组排序方法
    function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC  )
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
        return $arrays;
    }
}